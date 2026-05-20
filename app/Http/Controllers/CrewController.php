<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Crew;
use App\Models\Employee;
use App\Models\Trip;
use Illuminate\Http\Request;

class CrewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('crews.index', [
            'crews' => Crew::with(['assignments' => function ($query) {
                $query->whereHas('trip', function ($tripQuery) {
                    $tripQuery->where('depart_time', '>=', now()->subHours(12));
                })->with('trip');
            }])->paginate(15)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tripsValid = Trip::where('depart_time', '>=', now())->doesntHave('assignment')->get();

        return view('crews.create', ['employees' => Employee::all()->where('crew_id', '=', null), 'trips' => $tripsValid]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
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

        $crew = Crew::create();

        if (isset($organizedData['employees'])) {
            foreach ($organizedData['employees'] as $index => $selectedValue) {
                Employee::where('id', $selectedValue)->update(['crew_id' => $crew->id]);
            }
        }

        if (isset($organizedData['trips'])) {
            foreach ($organizedData['trips'] as $index => $selectedValue) {
                Assignment::create([
                    'crew_id' => $crew->id,
                    'trip_id' => $selectedValue
                ]);
            }
        }

        return redirect()->route('crews.show', $crew)->with('success', 'Бригада була створена');
    }

    /**
     * Display the specified resource.
     */
    public function show(Crew $crew)
    {
        $crew->load([
            'assignments' => function ($query) {
                $query->whereHas('trip', function ($tripQuery) {
                    $tripQuery->where('depart_time', '>=', now()->subHours(24));
                })->with('trip');
            }
        ]);

        return view('crews.show', ['crew' => $crew]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Crew $crew)
    {
        return view('crews.edit', $crew);
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
    public function destroy(Crew $crew)
    {
        foreach ($crew->employees as $employee) {
            $employee->update(['crew_id' => null]);
        }
        $crew->delete();

        return redirect()->route('crews.index')->with('success', 'Бригада була видалена');
    }
}
