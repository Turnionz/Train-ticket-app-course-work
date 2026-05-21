<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $filters = $request->validate([
            'from' => 'nullable|string',
            'to'   => 'nullable|string',
            'date' => 'nullable|date'
        ]);

        $tickets = $user->tickets()
            ->with(['trip.train', 'seat', 'departingStation', 'arrivalStation'])
            ->when($request->filled('from'), function ($query) use ($filters) {
                $query->whereHas('departingStation', function ($q) use ($filters) {
                    $q->where('address', 'ilike', '%' . $filters['from'] . '%');
                });
            })
            ->when($request->filled('to'), function ($query) use ($filters) {
                $query->whereHas('arrivalStation', function ($q) use ($filters) {
                    $q->where('address', 'ilike', '%' . $filters['to'] . '%');
                });
            })
            ->when($request->filled('date'), function ($query) use ($filters) {
                $query->whereHas('trip', function ($q) use ($filters) {
                    $q->whereDate('depart_time', $filters['date']);
                });
            })
            ->latest()
            ->get();

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
        Gate::authorize('user-level');

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
        $tickets = Ticket::whereIn('seat_id', $request->seat_ids)->get();

        foreach ($tickets as $ticket) {
            Gate::authorize('owner', $ticket);
        }

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
    public function destroy(Ticket $ticket)
    {
        Gate::authorize('owner', $ticket);

        $ticket->delete();

        return back()->with('success', 'Ваш квиток було успішно скасовано.');
    }
}
