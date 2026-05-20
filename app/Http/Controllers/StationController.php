<?php

namespace App\Http\Controllers;

use App\Http\Requests\StationRequest;
use App\Models\ConnectedStations;
use App\Models\Station;
use Illuminate\Http\Request;

class StationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('stations.index', ['stations' => Station::paginate(100)]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('stations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StationRequest $request)
    {
        $validated = $request->validated();

        $station = Station::create([
            'address' => $validated['address'],
            'capacity' => $validated['capacity']
        ]);

        if (!empty($validated['stations'])) {
            self::attachNeighbour($station, $validated['stations']);
        }

        return redirect()->route('stations.index')->with('success', 'Станцію успішно створено!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Station $station)
    {
        $station->load(['connectionsAsA.stationB', 'connectionsAsB.stationA']);

        return view('stations.show', compact('station'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Station $station)
    {
        $station->load(['connectionsAsA.stationA', 'connectionsAsB.stationB']);

        return view('stations.edit', ['station' => $station]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StationRequest $request, Station $station)
    {
        $stationsAdd = [];
        $stationsRemove = [];

        // You need ->all() to loop through the request payload
        foreach ($request->all() as $key => $value) {
            if (str_starts_with($key, 'stations-add') && !empty($value)) {
                $stationsAdd[] = (int) $value;
            }

            if (str_starts_with($key, 'stations-remove') && !empty($value)) {
                $stationsRemove[] = (int) $value;
            }
        }

        if (!empty($stationsAdd)) {
            self::attachNeighbour($station, $stationsAdd);
        }

        if (!empty($stationsRemove)) {
            self::dettachNeighbour($station, $stationsRemove);
        }

        // validated() is a method, so it needs the parentheses
        $station->update($request->validated());

        return redirect()->route('stations.show', $station)->with('success', 'Станцію оновлено!');
    }

    public function registerNeighbour(Station $station, Request $request)
    {
        self::attachNeighbour($station, $request);

        return redirect()->route('stations.show', ['station' => $station])->with('success', 'Додано сусідню станцію!');
    }

    public function deregisterNeighbour(Station $station, Request $request)
    {
        // Wrap the single ID in an array so dettachNeighbour can process it
        self::dettachNeighbour($station, [$request->input('station_b')]);

        return redirect()->back()->with('success', 'Станції більше не сусіди!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Station $station)
    {
        $station->delete();

        return redirect()->route('stantions.index')->with('success', 'Станція видалена!');
    }

    private static function dettachNeighbour(Station $station, array $neighbourIds)
    {
        foreach ($neighbourIds as $neighbourId) {
            $neighbourId = (int) $neighbourId;

            if (!$neighbourId || $station->id === $neighbourId) {
                continue;
            }

            $stationA = min($station->id, $neighbourId);
            $stationB = max($station->id, $neighbourId);

            ConnectedStations::where('station_a', $stationA)
                ->where('station_b', $stationB)
                ->delete();
        }
    }

    protected static function attachNeighbour(Station $station, Request|array $request)
    {
        if (is_array($request)) {
            // We are passing $validated['stations'], so this block runs!
            foreach ($request as $key => $value) {

                // Optional: Prevent a station from connecting to itself
                if ($station->id == $value) continue;

                if ($station->id < $value) {
                    $station_a = $station->id;
                    $station_b = $value;
                } elseif ($station->id > $value) {
                    $station_a = $value;
                    $station_b = $station->id;
                }

                // Fix: Changed from create() to firstOrCreate()
                ConnectedStations::firstOrCreate([
                    'station_a' => $station_a,
                    'station_b' => $station_b,
                ]);
            }
        } else {
            // This handles your other route (adding a single neighbor later)
            $validatedRequest = $request->validate([
                'station_id' => 'required|integer'
            ]);

            // Optional: Prevent a station from connecting to itself
            if ($station->id == $validatedRequest['station_id']) {
                return;
            }

            if ($station->id < $validatedRequest['station_id']) {
                $station_a = $station->id;
                $station_b = $validatedRequest['station_id'];
            } elseif ($station->id > $validatedRequest['station_id']) {
                $station_a = $validatedRequest['station_id'];
                $station_b = $station->id;
            }

            // Fix: Changed from create() to firstOrCreate()
            ConnectedStations::firstOrCreate([
                'station_a' => $station_a,
                'station_b' => $station_b,
            ]);
        }
    }
}
