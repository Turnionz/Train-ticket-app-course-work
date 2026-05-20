<?php

namespace App\Http\Controllers;

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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update() {}

    public function registerNeighbour(Station $station, Request $request)
    {
        $now = now();
        $request = $request->validate([
            'station_id' => 'required|integer'
        ]);

        if ($station->id < $request['station_id']) {
            $station_a = $station->id;
            $station_b = $request['station_id'];
        } elseif ($station->id > $request['station_id']) {
            $station_a = $request['station_id'];
            $station_b = $station->id;
        }

        ConnectedStations::factory()->create([
            'station_a' => $station_a,
            'station_b' => $station_b,
            'created_at' => $now,
            'updated_at' => $now
        ]);

        return redirect()->route('stations.show', ['station' => $station])->with('success', 'Додано сусідню станцію!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
