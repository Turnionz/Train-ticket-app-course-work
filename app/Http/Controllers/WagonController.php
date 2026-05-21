<?php

namespace App\Http\Controllers;

use App\Models\Train;
use App\Models\Wagon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class WagonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('operator-level');
        return view('wagons.index', ['wagons' => Wagon::paginate(15)]);
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
        Gate::authorize('operator-level');

        $request = $request->validate([
            'amount' => 'integer|required',
            'type_select' => 'required',
            'class_select' => 'required',
            'train_number' => 'nullable|integer'
        ]);

        $wagonType = $request['type_select'];
        $layoutMap = Wagon::getPresets()[$wagonType] ?? [];
        $wagonSeatClass = $request['class_select'];

        $now = now();

        if ($request['train_number'] !== null) {
            $train = Train::find($request['train_number']);
            $wagonsCount = $train->count();
            for ($i = 0; $i < $request['amount']; $i++) {
                $wagonsCount++;

                $wagon = Wagon::factory()
                    ->for($train)
                    ->create([
                        'wagon_number' => $wagonsCount,
                        'type' => $wagonType,
                        'layout_map' => $layoutMap
                    ]);

                $seatNumber = 1;
                foreach ($layoutMap as $row) {
                    foreach ($row as $cell) {
                        if ($cell === 'seat') {
                            $allSeatsToInsert[] = [
                                'wagon_id' => $wagon->id,
                                'seat_number' => $seatNumber,
                                'class' => $wagonSeatClass,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ];
                            $seatNumber++;
                        }
                    }
                }
            }
        } else {
            for ($i = 0; $i < $request['amount']; $i++) {
                $wagon = Wagon::factory()
                    ->create([
                        'wagon_number' => null,
                        'type' => $wagonType,
                        'layout_map' => $layoutMap
                    ]);

                $seatNumber = 1;

                foreach ($layoutMap as $row) {
                    foreach ($row as $cell) {
                        if ($cell === 'seat') {
                            $allSeatsToInsert[] = [
                                'wagon_id' => $wagon->id,
                                'seat_number' => $seatNumber,
                                'class' => $wagonSeatClass,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ];
                            $seatNumber++;
                        }
                    }
                }
            }
        }

        return redirect()->route('wagons.index')->with('success', 'Вагон(и) був(ли) створен(и)');
    }

    /**
     * Display the specified resource.
     */
    public function show(Wagon $wagon)
    {
        Gate::authorize('operator-level');

        return view('wagons.show', ['wagon' => $wagon]);
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
    public function update(Request $request, Wagon $wagon)
    {
        Gate::authorize('operator-level');

        $request = $request->validate([
            'train_number' => 'nullable|integer'
        ]);

        if ($request['train_number'] === null) {
            $wagon->update(['train_id' => null]);
        } else {
            $train = Train::where('train_number', '=', $request['train_number'])->first();
        }

        if ($train) {
            $wagon->update(['train_id' => $train->id]);
            return redirect()->route('wagons.show', $wagon)->with('success', 'Вагон редаговано');
        } else {
            return redirect()->route('wagons.show', $wagon)->with('error', 'Потяга з таким номером немає!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Wagon $wagon)
    {
        Gate::authorize('operator-level');

        $hasTickets = \App\Models\Ticket::whereHas('seat', function ($query) use ($wagon) {
            $query->where('wagon_id', $wagon->id);
        })->exists();

        if ($hasTickets) {
            return back()->with('error', 'Помилка: не можна видалити вагон, оскільки у ньому є продані або заброньовані квитки!');
        }
        $wagon->seats()->delete();

        $wagon->delete();

        return redirect()->route('wagons.index')->with('success', 'Вагон та всі його місця були успішно видалені.');
    }
}
