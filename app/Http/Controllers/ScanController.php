<?php

namespace App\Http\Controllers;

use App\Models\Scan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ScanController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $scans = Auth::user()->scans()->latest('last_update')->get();
        return view('scan.index', compact('scans'));
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
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('scans')->where(function ($query) {
                    return $query->where('user_id', auth()->id());
                })
            ],
            'summary' => 'nullable|string',
            'current_chapter' => 'required|numeric|min:0',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'link_to_scan' => 'nullable|url'
        ]);

        $validated['user_id'] = auth()->id();

        if ($request->hasFile('cover_image')) {
            $image = $request->file('cover_image');
            $path = $image->store('images', 'public');
            $validated['cover_image'] = $path;
        }

        // Add timestamps
        $validated['create_date'] = now();
        $validated['last_update'] = now();

        // Create the scan record
        $scan = Scan::create($validated);

        return redirect()->route('scan.index')
            ->with('success', 'Scan created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Scan $scan)
    {
        $this->authorize('view', $scan);
        return view('scan.show', compact('scan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Scan $scan)
    {
        $this->authorize('update', $scan);
        return view('scan.edit', compact('scan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Scan $scan)
    {
        $this->authorize('update', $scan);

        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('scans')->where(function ($query) {
                    return $query->where('user_id', auth()->id());
                })->ignore($scan->id)
            ],
            'summary' => 'nullable|string',
            'current_chapter' => 'required|numeric|min:0',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'link_to_scan' => 'nullable|url'
        ]);

        if ($request->hasFile('cover_image')) {
            // Delete old image if exists
            if ($scan->cover_image) {
                Storage::disk('public')->delete($scan->cover_image);
            }
            
            $path = $request->file('cover_image')->store('covers', 'public');
            $validated['cover_image'] = $path;
        }

        // Add timestamps
        $validated['last_update'] = now();
        
        // Update the scan
        $scan->update($validated);
        
        // Redirect back to the previous page
        return redirect()->back()->with('success', 'Scan updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Scan $scan)
    {
        $this->authorize('delete', $scan);
        Scan::destroy($scan->id);
        return redirect()->route('scan.index')->with('success', 'Scan removed successfully.');
    }

    /**
     * Check if a title already exists
     */
    public function checkTitle(Request $request)
    {
        $exists = Scan::where('title', $request->title)->exists();
        return response()->json(['exists' => $exists]);
    }

    public function updateChapter(Request $request, Scan $scan)
    {
        $this->authorize('update', $scan);
        try {
            $validated = $request->validate([
                'current_chapter' => 'required|numeric|min:0'
            ]);

            $scan->update([
                'current_chapter' => $validated['current_chapter']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Chapter updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating chapter'
            ], 500);
        }
    }
}
