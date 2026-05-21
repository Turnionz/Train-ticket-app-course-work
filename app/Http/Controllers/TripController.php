<?php

namespace App\Http\Controllers;

use App\Models\Seat;
use App\Models\Trip;
use Illuminate\Http\Request;

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

        $trips = Trip::query()
            ->when($request->filled('from'), function ($query) use ($filters) {
                $query->whereHas('route.departStation', function ($q) use ($filters) {
                    $q->where('address', 'ilike', '%' . $filters['from'] . '%');
                });
            })
            ->when($request->filled('to'), function ($query) use ($filters) {
                $query->whereHas('route.arrivalStation', function ($q) use ($filters) {
                    $q->where('address', 'ilike', '%' . $filters['to'] . '%');
                });
            })
            ->when($request->filled('date'), function ($query) use ($filters) {
                $query->whereDate('departure_time', $filters['date']);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('trips.index', compact('trips'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        dd($request);
        return redirect()->route('tickets.view')->with('success', 'Ви купили квитки');
    }

    /**
     * Display the specified resource.
     */
    public function show(Trip $trip)
    {
        return view('trips.show', ['trip' => $trip->load([
            'train',
            'route.departStation',
            'route.arrivalStation'
        ])]);
    }

    public function details(Request $request)
    {
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
}
