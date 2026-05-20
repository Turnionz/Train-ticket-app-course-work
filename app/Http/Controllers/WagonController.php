<?php

namespace App\Http\Controllers;

use App\Models\Train;
use App\Models\Wagon;
use Illuminate\Http\Request;

class WagonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('wagons.index', ['wagons' => Wagon::with('seats')->paginate(15)]);
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

                // Loop through the layout map to generate seats for this specific wagon
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

                // Loop through the layout map to generate seats for this specific wagon
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
