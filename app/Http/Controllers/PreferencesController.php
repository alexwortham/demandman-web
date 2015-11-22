<?php

/**
 *
 */
namespace App\Http\Controllers;
use App\Model\UserPreference;
use Illuminate\Http\Request;

/**
 *
 */
class PreferencesController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('preferences/index', ['preferences' => UserPreference::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('preferences/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $preference = new UserPreference();

        $preference->name = $request->name;
        $preference->ui_name = $request->ui_name;
        $preference->value = $request->value;

        $preference->save();

        return redirect()->route('preferences.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        return view('preferences/edit', ['preference' => UserPreference::find($id)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $preference = UserPreference::find($id);

        $preference->name = $request->name;
        $preference->ui_name = $request->ui_name;
        $preference->value = $request->value;

        $preference->save();

        return redirect()->route('preferences.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {

    }

}

?>
