<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\Seat;
use App\Models\Station;
use App\Models\Ticket;
use App\Models\Trip;
use Carbon\Carbon;
use GuzzleHttp\Psr7\Request as Psr7Request;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TripController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->validate([
            'from' => 'nullable|string',
            'to'   => 'nullable|string',
            'date' => 'nullable|date'
        ]);

        $query = Trip::with([
            'train.wagons',
            'train.seats',
            'route.departStation',
            'route.arrivalStation'
        ])
            ->withCount(['tickets' => function ($query) {
                $query->whereIn('status', ['reserved', 'booked']);
            }]);

        $trips = $query
            ->when($request->filled('from'), function ($query) use ($filters) {
                $query->whereHas('route.routeStops.station', function ($q) use ($filters) {
                    $q->where('address', 'like', '%' . $filters['from'] . '%');
                });
            })
            ->when($request->filled('to'), function ($query) use ($filters) {
                $query->whereHas('route.routeStops.station', function ($q) use ($filters) {
                    $q->where('address', 'like', '%' . $filters['to'] . '%');
                });
            })
            ->when($request->filled('date'), function ($query) use ($filters) {
                $query->whereDate('depart_time', $filters['date']);
            })
            ->latest()
            ->where('depart_time', '>=', now())
            ->paginate(15)
            ->withQueryString();

        return view('trips.index', compact('trips'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('trips.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'ticket_ids'   => 'required|array',
            'ticket_ids.*' => 'exists:tickets,id',
        ]);

        $tickets = Ticket::whereIn('id', $request->ticket_ids)->get();

        foreach ($tickets as $ticket) {
            Gate::authorize('owner', $ticket);
        }

        Ticket::whereIn('id', $request->ticket_ids)->update([
            'status' => 'booked'
        ]);

        return redirect()->route('tickets.index')->with('success', 'Ви успішно купили квитки!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Trip $trip)
    {
        $trip->load([
            'route.departStation',
            'route.arrivalStation',
            'train.wagons.seats.tickets' => function ($query) use ($trip) {
                $query->where('trip_id', $trip->id)
                    ->whereIn('status', ['reserved', 'booked']);
            }
        ]);

        return view('trips.show', compact('trip'));
    }

    public function details(Request $request)
    {
        Gate::authorize('user-level');

        $validated = $request->validate([
            'trip_id' => 'required|exists:trips,id',
            'seat_ids' => 'required|array|min:1',
            'seat_ids.*' => 'exists:seats,id',
        ]);

        $seats = Seat::with('wagon.train')->whereIn('id', $validated['seat_ids'])->get();
        $trip_id = $validated['trip_id'];

        $trip = Trip::with(['route.routeStops.station'])->findOrFail($trip_id);

        $availableStations = $trip->route->routeStops
            ->sortBy('order')
            ->map(function ($stop) {
                return $stop->station;
            })
            ->filter()
            ->values();

        return view('trips.details', compact('seats', 'trip_id', 'availableStations'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    public function tripCreate(Request $request)
    {

        Gate::authorize('operator-level');

        $validated = $request->validate([
            'trains-0' => 'required|integer',
            'route-0'  => 'required|integer',
            'date'     => 'required|date',
        ]);

        $trainId = $validated['trains-0'];
        $routeId = $validated['route-0'];

        $desiredDepartTime = Carbon::parse($validated['date']);

        $desiredRoute = Route::with('routeStops.station')->findOrFail($routeId);
        $expectedArrivalTime = $this->expectedArrivalTime($desiredDepartTime, $desiredRoute);

        // Find existing active trips in that timeframe
        $activeTrips = Trip::with('route.routeStops.station')
            ->where('arrival_time', '>=', $desiredDepartTime)
            ->where('depart_time', '<=', $expectedArrivalTime)
            ->get();

        // Check if the physical train is already scheduled during this timeframe
        if ($activeTrips->contains('train_id', $trainId)) {
            return redirect()->back()->with('error', 'Потяг зайнятий в цей час!');
        }

        $desiredStationIds = $desiredRoute->routeStops->pluck('station_id')->unique();

        $activeTripStationIds = $activeTrips->flatMap(function ($trip) {
            return $trip->route->routeStops->pluck('station_id');
        })->unique();

        $sharedStationIds = $desiredStationIds->intersect($activeTripStationIds);

        if ($sharedStationIds->isNotEmpty()) {

            $sharedStations = Station::whereIn('id', $sharedStationIds)->get();

            foreach ($sharedStations as $station) {


                $desiredStop = $desiredRoute->routeStops->firstWhere('station_id', $station->id);

                $newTrainArrive = $this->countTimeWhenArrivesAt($desiredDepartTime, $desiredRoute, $station);

                // Add 5 minutes buffer
                $stopTimeMinutes = $desiredStop->stop_time ?? 0;
                $newTrainDepart = $newTrainArrive->copy()->addMinutes($stopTimeMinutes + 5);

                $amountOfTrainsAtStation = 0;

                foreach ($activeTrips as $trip) {
                    // Does this active trip stop at this station?
                    $tripStop = $trip->route->routeStops->firstWhere('station_id', $station->id);

                    if ($tripStop) {
                        $tripDepartTime = Carbon::parse($trip->depart_time);

                        $tripArrive = $this->countTimeWhenArrivesAt($tripDepartTime, $trip->route, $station);
                        $tripStopTimeMinutes = $tripStop->stop_time ?? 0;
                        $tripDepart = $tripArrive->copy()->addMinutes($tripStopTimeMinutes + 5);

                        if ($tripArrive->lte($newTrainDepart) && $tripDepart->gte($newTrainArrive)) {
                            $amountOfTrainsAtStation++;
                        }
                    }
                }
                if ($amountOfTrainsAtStation >= $station->capacity) {
                    return redirect()->back()->withErrors([
                        'capacity' => "Потяг не може зупинитися на станції {$station->station_address} о {$newTrainArrive->format('Y-m-d H:i')} (Немає місця)"
                    ])->withInput();
                }
            }
        }

        $trip = Trip::create([
            'train_id'     => $trainId,
            'route_id'     => $routeId,
            'depart_time'  => $desiredDepartTime,
            'arrival_time' => $expectedArrivalTime,
        ]);

        return redirect()->route('trips.show', $trip)->with('success', 'Рейс створено успішно!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    private function expectedArrivalTime(Carbon $departTime, Route $route): Carbon
    {
        return $departTime->copy()->addHours(2);
    }

    private function countTimeWhenArrivesAt(Carbon $departTime, Route $route, Station $station): Carbon
    {
        return $departTime->copy()->addMinutes(30);
    }
}
