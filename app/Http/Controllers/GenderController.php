<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gender;

class GenderController extends Controller
{
    public function index()
    {
        $genders = Gender::all();
        return view('gender.index', compact('genders'));
    }

    public function store(Request $request)
    {
        
        try {
            $request->validate([
                'name' => 'required|unique:genders|max:255',
            ]);
            Gender::create($request->all());
            return redirect()->route('gender.index')->with('success', 'Gender created successfully');
        } catch (\Exception $e) {
            return redirect()->route('gender.index')->with('error', 'Failed to create gender. Title must be unique.');
        }
    }

    public function destroy(Gender $gender)
    {
        $gender->delete();

        return redirect()->route('gender.index')->with('success', 'Gender deleted successfully');
    }
}
