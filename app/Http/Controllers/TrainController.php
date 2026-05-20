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
