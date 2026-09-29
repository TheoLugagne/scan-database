<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Genre;

class GenreController extends Controller
{
    public function index()
    {
        $genres = Genre::all();
        return view('genre.index', compact('genres'));
    }

    public function store(Request $request)
    {
        
        try {
            $request->validate([
                'name' => 'required|unique:genres|max:255',
            ]);
            Genre::create($request->all());
            return redirect()->route('genre.index')->with('success', 'Genre created successfully');
        } catch (\Exception $e) {
            return redirect()->route('genre.index')->with('error', 'Failed to create genre. Title must be unique.');
        }
    }

    public function destroy(Genre $genre)
    {
        $genre->delete();

        return redirect()->route('genre.index')->with('success', 'Genre deleted successfully');
    }
}
