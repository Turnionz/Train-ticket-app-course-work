<?php

namespace App\Http\Controllers;

use App\Models\Crew;
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
    public function destroy(string $id)
    {
        //
    }
}
