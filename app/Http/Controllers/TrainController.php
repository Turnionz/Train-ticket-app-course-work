<?php

namespace App\Http\Controllers;

use App\Models\Train;
use App\Models\Wagon;
use Illuminate\Http\Request;

class TrainController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('trains.index', ['trains' => Train::with(['trip', 'wagons', 'seats'])->paginate(15)]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('trains.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'train_number' => 'required|string|max:10',
            'type' => 'required|string',
        ];

        $organizedData = [];

        // Organize and validate data   
        foreach ($request->except(['_token', '_method', 'train_number', 'type']) as $key => $value) {
            if (str_contains($key, '-')) {
                if ($value !== null && $value !== '') {
                    // Splits keys like 'wagonAlt-0' into 'wagonAlt' and '0'
                    [$name, $index] = explode('-', $key, 2);

                    $rules[$key] = 'required';
                    $organizedData[$name][$index] = $value;
                }
            }
        }

        $request->validate($rules);

        $train = Train::create([
            // If type comes through as an array (from some selects), grab the first item, otherwise use it directly
            'type' => is_array($request->type) ? $request->type[0] : $request->type,
            'train_number' => $request->input('train_number')
        ]);

        $wagonsCount = 0;
        $wagonPresets = Wagon::getPresets();
        $allSeatsToInsert = [];
        $now = now();

        // Attach Existing Wagons 
        if (!empty($organizedData['wagons'])) {
            foreach ($organizedData['wagons'] as $index => $wagonId) {
                $wagonsCount++;

                Wagon::where('id', $wagonId)->update([
                    'train_id' => $train->id,
                    'wagon_number' => $wagonsCount
                ]);
            }
        }

        // Create new wasgons
        if (!empty($organizedData['wagonAlt'])) {
            foreach ($organizedData['wagonAlt'] as $index => $countToCreate) {

                // Extract the matching type and seat class for this specific row index
                $wagonType = $organizedData['wagon'][$index] ?? 'Standard';
                $wagonSeatClass = $organizedData['seat'][$index] ?? 'Standard';

                // Get the specific layout for this wagon type
                $layoutMap = $wagonPresets[$wagonType] ?? [];

                // Loop to create the requested number of identical wagons
                for ($i = 0; $i < $countToCreate; $i++) {
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
            }
        }

        if (!empty($allSeatsToInsert)) {
            \App\Models\Seat::insert($allSeatsToInsert);
        }

        return redirect()->route('trains.show', $train)->with('success', 'Потяг успішно створено!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Train $train)
    {
        return view('trains.show', ['train' => $train->load('wagons')]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Train $train)
    {
        return view('trains.edit', ['train' => $train]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Train $train)
    {
        $rules = [];
        $organizedData = [];

        foreach ($request->except('_token') as $key => $value) {
            if (str_contains($key, '-')) {

                if ($value !== null && $value !== '') {
                    [$name, $index] = explode('-', $key, 2);

                    $rules[$key] = 'required|integer';
                    $organizedData[$name][$index] = $value;
                }
            }
        }

        $request->validate($rules);

        $wagonsCount = $train->wagons->count() + 1;

        if (isset($organizedData['wagons'])) {
            foreach ($organizedData['wagons'] as $index => $selectedValue) {
                Wagon::where('id', $selectedValue)->update(['train_id' => $train->id, 'wagon_number' => $wagonsCount]);
                $wagonsCount++;
            }
        }

        return redirect()->route('trains.show', $train)->with('success', 'Вагон(и) був(лм) додан(і)');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Train $train)
    {
        foreach ($train->wagons as $wagon) {
            $wagon->update(['train_id' => null, 'wagon_number' => null]);
        }
        $train->delete();

        return redirect()->route('trains.index')->with('success', 'Дія була виконана успішно');
    }
}
