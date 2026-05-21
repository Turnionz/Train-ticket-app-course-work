<?php

namespace App\Http\Controllers;

use App\Models\Train;
use App\Models\Wagon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TrainController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('operator-level');

        return view('trains.index', ['trains' => Train::with(['trip', 'wagons', 'seats'])->paginate(15)]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('operator-level');

        return view('trains.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('operator-level');

        $rules = [
            'train_number' => 'required|string|max:10',
            'type' => 'required|string',
        ];

        $organizedData = [];

        foreach ($request->except(['_token', '_method', 'train_number', 'type']) as $key => $value) {
            if (str_contains($key, '-')) {
                if ($value !== null && $value !== '') {
                    [$name, $index] = explode('-', $key, 2);

                    $rules[$key] = 'required';
                    $organizedData[$name][$index] = $value;
                }
            }
        }

        $request->validate($rules);

        $train = Train::create([
            'type' => is_array($request->type) ? $request->type[0] : $request->type,
            'train_number' => $request->input('train_number')
        ]);

        $wagonsCount = 0;
        $wagonPresets = Wagon::getPresets();
        $allSeatsToInsert = [];
        $now = now();

        if (!empty($organizedData['wagons'])) {
            foreach ($organizedData['wagons'] as $index => $wagonId) {
                $wagonsCount++;

                Wagon::where('id', $wagonId)->update([
                    'train_id' => $train->id,
                    'wagon_number' => $wagonsCount
                ]);
            }
        }

        if (!empty($organizedData['wagonAlt'])) {
            foreach ($organizedData['wagonAlt'] as $index => $countToCreate) {

                $wagonType = $organizedData['wagon'][$index] ?? 'Standard';
                $wagonSeatClass = $organizedData['seat'][$index] ?? 'Standard';

                $layoutMap = $wagonPresets[$wagonType] ?? [];

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
        Gate::authorize('operator-level');

        return view('trains.show', ['train' => $train->load('wagons')]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Train $train)
    {
        Gate::authorize('operator-level');

        return view('trains.edit', ['train' => $train]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Train $train)
    {
        Gate::authorize('operator-level');

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
        Gate::authorize('operator-level');

        $hasTickets = \App\Models\Ticket::whereHas('seat.wagon', function ($query) use ($train) {
            $query->where('train_id', $train->id);
        })->exists();

        if ($hasTickets) {
            return back()->with('error', 'Помилка: не можна видалити потяг, оскільки у його вагонах є продані квитки!');
        }

        foreach ($train->wagons as $wagon) {
            $wagon->update(['train_id' => null, 'wagon_number' => null]);
        }

        $train->delete();

        return redirect()->route('trains.index')->with('success', 'Дія була виконана успішно');
    }
}
