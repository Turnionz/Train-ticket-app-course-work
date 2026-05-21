<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $tickets = $user->tickets;

        return view('tickets.index', ['tickets' => $tickets]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function buy(Request $request)
    {
        // 1. Валідація даних
        $validated = $request->validate([
            'trip_id' => 'required|exists:trips,id',
            'seat_ids' => 'required|array|min:1',
            'seat_ids.*' => 'exists:seats,id',
        ], [
            'seat_ids.required' => 'Будь ласка, оберіть хоча б одне місце.',
        ]);

        $tripId = $validated['trip_id'];
        $seatIds = $validated['seat_ids'];

        $departStations = [];
        foreach ($request->input('depart', []) as $key => $value) {
            $cleanKey = preg_replace('/[^0-9]/', '', $key);
            $departStations[$cleanKey] = $value;
        }


        $arriveStations = [];
        foreach ($request->input('arrive', []) as $key => $value) {
            $cleanKey = preg_replace('/[^0-9]/', '', $key);
            $arriveStations[$cleanKey] = $value;
        }

        $createdTickets = [];


        foreach ($seatIds as $seatId) {

            $depart = $departStations[$seatId] ?? null;
            $arrive = $arriveStations[$seatId] ?? null;

            $ticket = Ticket::create([
                'seat_id' => $seatId,
                'trip_id' => $tripId,
                'status'  => Ticket::$status[0],

                'departing_station' => is_array($depart) ? $depart[0] : $depart,
                'arriving_station'   => is_array($arrive) ? $arrive[0] : $arrive,

                'user_id' => auth()->id(),
            ]);

            $createdTickets[] = $ticket;
        }

        return view('trips.payment', ['tickets' => $createdTickets]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        foreach ($request->seat_ids as $key => $seat) {
            Ticket::where('seat_id', '=', $seat)->update([
                'status' => Ticket::$status[1]
            ]);
        }

        return redirect()->route('tickets.index')->with('success', 'Білети оформлени');
    }

    /**
     * Display the specified resource.
     */
    public function show() {}

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
