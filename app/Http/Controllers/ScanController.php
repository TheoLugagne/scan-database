<?php

namespace App\Http\Controllers;

use App\Models\Scan;
use Illuminate\Http\Request;

class ScanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('scan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255|unique:scans,title',
            'summary' => 'nullable|string',
            'current_chapter' => 'required|numeric|min:0',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'link_to_scan' => 'nullable|url'
        ]);

        if ($request->hasFile('cover_image')) {
            $image = $request->file('cover_image');
            $path = $image->store('images', 'public');
            $validatedData['cover_image'] = $path;
        }

        // Add timestamps
        $validatedData['create_date'] = now();
        $validatedData['last_update'] = now();

        // Create the scan record
        $scan = Scan::create($validatedData);

        return redirect()->route('scan.index')
            ->with('success', 'Scan created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(scan $scan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(scan $scan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, scan $scan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(scan $scan)
    {
        //
    }

    /**
     * Check if a title already exists
     */
    public function checkTitle(Request $request)
    {
        $exists = Scan::where('title', $request->title)->exists();
        return response()->json(['exists' => $exists]);
    }
}
