<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use App\Http\Requests\LocationFormRequest;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Location::paginate();

        return inertia()->render('Location/Index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return inertia()->render('Location/Form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\LocationFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LocationFormRequest $request)
    {
        $location = Location::create($request->all());

        return redirect()->route('locations.show', $location);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function show(Location $location)
    {
        return inertia()->render('Location/Detail', compact('location'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function edit(Location $location)
    {
        return inertia()->render('Location/Form', compact('location'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\LocationFormRequest  $request
     * @param  \App\Models\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function update(LocationFormRequest $request, Location $location)
    {
        $location->update($request->all());

        return redirect()->route('locations.show', $location);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function destroy(Location $location)
    {
        $location->delete();

        return redirect()->route('locations.index');
    }

    /**
     * Search location for list items.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        $query = $request->get('query');

        $data = Location::where('code', 'like', "%${query}%")
            ->limit(15)
            ->get();

        return response()->json($data);
    }
}
