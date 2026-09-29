<?php

namespace App\Http\Controllers;

use App\Models\Scan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\UserScanProgress;
use App\Models\Gender;
use App\Models\ScanStatus;
use App\Models\ReadingStatus;
use Illuminate\Support\Facades\Log;

class ScanController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = max(1, (int) $request->input('per_page', 12));
        $scans = $this->filteredScanQuery($request)
            ->with('genders')
            ->latest('updated_at')
            ->paginate($perPage);

        return view('scan.index', compact('scans', 'perPage'));
    }

    public function fetch(Request $request)
    {
        $perPage = max(1, (int) $request->input('per_page', 12));
        $scans = $this->filteredScanQuery($request)
            ->with('genders')
            ->latest('updated_at')
            ->paginate($perPage);

        return view('scan.partials.scan-list', compact('scans'))->render();
    }

    /**
     * Scans visible for the current search, status, gender, and reading-status filters.
     * Reading status is limited to the logged-in user's progress.
     */
    private function filteredScanQuery(Request $request)
    {
        $search = $request->input('search', '');
        $gender_ids = $request->input('gender_ids', '');
        $status = $request->input('status', '');
        $reading_status = $request->input('reading_status', '');

        $query = Scan::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('link_to_scan', 'like', '%' . $search . '%');
            });
        }

        if ($gender_ids) {
            $genderIdsArray = is_array($gender_ids)
                ? array_map('intval', $gender_ids)
                : array_map('intval', explode(',', $gender_ids));
            $genderIdsArray = array_filter($genderIdsArray);

            if (!empty($genderIdsArray)) {
                $query->whereHas('genders', function ($q) use ($genderIdsArray) {
                    $q->whereIn('genders.id', $genderIdsArray);
                });
            }
        }

        if ($status) {
            $query->where('status', ScanStatus::from($status));
        }

        if ($reading_status) {
            $userId = Auth::id();
            $query->whereHas('userScanProgress', function ($q) use ($reading_status, $userId) {
                $q->where('user_scan_progress.user_id', $userId)
                    ->where('user_scan_progress.reading_status', ReadingStatus::from($reading_status));
            });
        }

        return $query;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $genders = Gender::all();
        $status_list = ScanStatus::all();
        return view('scan.create', ['genders' => $genders, 'status' => $status_list]);
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
                'link_to_scan' => 'nullable|url',
                'gender_ids' => 'nullable|string|regex:/^\d+(,\d+)*$/',
                'status' => ['required', Rule::enum(ScanStatus::class)],
            ]);

            $validated['user_id'] = auth()->id();
            
            if ($request->hasFile('cover_image')) {
                $image = $request->file('cover_image');
                $path = $image->store('images', 'public');
                $validated['cover_image'] = $path;
            }
            
            // Create the scan record
            $scan = Scan::create($validated);
            
            // attach genders to the scan
            if (!empty($validated['gender_ids'])) {
                $validated['gender_ids'] = explode(',', $validated['gender_ids']);
                $scan->genders()->attach($validated['gender_ids']);
            }
            
            if (auth()->check()) {
                // create user scan progress record via controller
                $userScanProgress = new UserScanProgressController();
                $request = new Request([
                    'user_id' => auth()->id(),
                    'scan_id' => $scan->id,
                    'current_chapter' => $request->input('current_chapter', 0),
                    'reading_status' => $request->input('reading_status', ReadingStatus::NOT_STARTED)
                ]);
                $userScanProgress->store($request);
            }

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
        $scan->load('genders');
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
        $genders = Gender::all();
        $status_list = ScanStatus::all();
        return view('scan.edit', compact('scan', 'genders', 'status_list'));
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
            'link_to_scan' => 'nullable|url',
            'gender_ids' => 'nullable|string|regex:/^\d+(,\d+)*$/',
            'status' => ['required', Rule::enum(ScanStatus::class)],
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
            if ($field === 'gender_ids') {
                // Special handling for gender_ids - compare with actual relationships
                $currentGenderIds = $scan->genders->pluck('id')->sort()->values()->toArray();
                $newGenderIds = is_array($value) ? $value : explode(',', $value);
                $newGenderIds = array_map('intval', $newGenderIds);
                sort($newGenderIds);
                
                if ($currentGenderIds !== $newGenderIds) {
                    $hasChanges = true;
                    break;
                }
            } else {
                // Regular field comparison
                if ($scan->$field != $value) {
                    $hasChanges = true;
                    break;
                }
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
            // update genders
            if (!empty($validated['gender_ids'])) {
                $validated['gender_ids'] = explode(',', $validated['gender_ids']);
                $scan->genders()->sync($validated['gender_ids']);
            }
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
