<?php

namespace App\Http\Controllers;

use App\Models\Scan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\UserScanProgress;

class ScanController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 12);
        
        // Get all scans and paginate them
        $scans = Scan::latest('updated_at')->paginate($perPage);
        
        return view('scan.index', compact('scans', 'perPage'));
    }

    public function fetch(Request $request)
    {
        // Clean and validate the per_page parameter
        $perPage = (int) $request->input('per_page', 12);
        // Ensure it's at least 1
        $perPage = max(1, $perPage);

        $search = $request->input('search', '');

        $query = Scan::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('link_to_scan', 'like', '%' . $search . '%');
            });
        }

        $scans = $query->latest('updated_at')
            ->paginate($perPage);

        return view('scan.partials.scan-list', compact('scans'))->render();
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
        try {
            $validated = $request->validate([
                'title' => 'unique:scans,title|required|string|max:255',
                'summary' => 'nullable|string',
                'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'link_to_scan' => 'nullable|url'
            ]);

            $validated['user_id'] = auth()->id();

            if ($request->hasFile('cover_image')) {
                $image = $request->file('cover_image');
                $path = $image->store('images', 'public');
                $validated['cover_image'] = $path;
            }

            // Create the scan record
            $scan = Scan::create($validated);

            // create user scan progress record via controller
            $userScanProgress = new UserScanProgressController();
            $request = new Request([
                'user_id' => auth()->id(),
                'scan_id' => $scan->id,
                'current_chapter' => $request->input('current_chapter', 0)
            ]);
            $userScanProgress->store($request);

            return redirect()->route('scan.index')
                ->with('success', 'Scan created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'An error occurred while creating the scan.'])
                ->withInput();
        }
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
        
        // Only store previous URL if it's not from the edit page
        $previousUrl = url()->previous();
        if (!str_contains($previousUrl, '/scan/' . $scan->id . '/edit')) {
            session(['scan_previous_url' => $previousUrl]);
        }
        
        return view('scan.edit', compact('scan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Scan $scan)
    {
        $this->authorize('update', $scan);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string',
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
        
        // Check if any values are actually different
        $hasChanges = false;
        foreach ($validated as $field => $value) {
            if ($scan->$field != $value) {
                $hasChanges = true;
                break;
            }
        }

        // If no changes were made
        if (!$hasChanges) {
            return redirect()->back()
                ->with('info', 'No changes were made to the scan.');
        }
        
        try {
            // Update the scan
            $scan->update($validated);
            return redirect()->back()
                ->with('success', 'Scan updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update scan. Please try again.')
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Scan $scan)
    {
        $this->authorize('delete', $scan);
        // Delete the cover image if it exists
        if ($scan->cover_image) {
            Storage::disk('public')->delete($scan->cover_image);
        }
        $scan->delete();
        return redirect()->route('scan.index')->with('success', 'Scan removed successfully.');
    }
}
