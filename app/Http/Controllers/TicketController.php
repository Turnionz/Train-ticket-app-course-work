<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function buy(Request $request, Trip $trip)
    {
        // Validate that at least one seat was selected
        $validated = $request->validate([
            'seat_ids' => 'required|array|min:1',
            'seat_ids.*' => 'exists:seats,id',
        ], [
            'seat_ids.required' => 'Будь ласка, оберіть хоча б одне місце.',
        ]);

        // return dd($validated);

        // Here you would add your logic to:
        // 1. Verify the seats aren't already taken by someone else 
        // 2. Calculate the total price
        // 3. Create the tickets or redirect to a payment page

        // Example redirect to checkout:
        // return redirect()->route('checkout.index', ['seats' => $request->seat_ids]);
        $tickets = $request->input('seat_ids');

        // Передаємо у вид
        return view('trips.payment', compact('tickets'));
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
    public function show(string $id)
    {
        //
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
