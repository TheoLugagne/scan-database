<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\UserScanProgress;    
use App\Models\Scan;
use App\Models\ReadingStatus;
use App\Models\ScanStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserScanProgressController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', UserScanProgress::class);
        $perPage = max(1, (int) $request->input('per_page', 12));

        $userScanProgress = $this->filteredProgressQuery($request)
            ->latest('user_scan_progress.updated_at')
            ->paginate($perPage);

        return view('userScanProgress.index', compact('userScanProgress', 'perPage'));
    }

    public function fetch(Request $request)
    {
        $perPage = max(1, (int) $request->input('per_page', 12));

        $userScanProgress = $this->filteredProgressQuery($request)
            ->latest('user_scan_progress.updated_at')
            ->paginate($perPage);

        return view('userScanProgress.partials.scan-progress-list', compact('userScanProgress'))->render();
    }

    /**
     * The current user's progress, with the same search and filters as the catalog.
     * user_id is qualified because the query joins scans, which also has that column.
     */
    private function filteredProgressQuery(Request $request)
    {
        $search = $request->input('search', '');
        $readingStatus = $request->input('reading_status', '');
        $status = $request->input('status', '');
        $genderIds = $request->input('gender_ids', '');

        $query = UserScanProgress::query()
            ->where('user_scan_progress.user_id', Auth::id())
            ->with(['scan.genders'])
            ->join('scans', 'user_scan_progress.scan_id', '=', 'scans.id')
            ->select('user_scan_progress.*');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('scans.title', 'like', '%' . $search . '%')
                    ->orWhere('scans.link_to_scan', 'like', '%' . $search . '%');
            });
        }

        if ($readingStatus) {
            $query->where('user_scan_progress.reading_status', $readingStatus);
        }

        if ($status) {
            $query->where('scans.status', $status);
        }

        if ($genderIds) {
            $genderIdsArray = is_array($genderIds)
                ? array_map('intval', $genderIds)
                : array_map('intval', explode(',', $genderIds));
            $genderIdsArray = array_filter($genderIdsArray);

            if (!empty($genderIdsArray)) {
                $query->whereHas('scan.genders', function ($q) use ($genderIdsArray) {
                    $q->whereIn('genders.id', $genderIdsArray);
                });
            }
        }

        return $query;
    }

    public function create(Scan $scan)
    {
        $this->authorize('create', UserScanProgress::class);
        $user = Auth::user();
        $reading_status_list = ReadingStatus::all();
        $scan_status_list = ScanStatus::all();
        return view('userScanProgress.create', compact('scan', 'user', 'reading_status_list', 'scan_status_list'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'scan_id' => 'required|exists:scans,id',
            'current_chapter' => 'required|numeric|min:0',
            'reading_status' => ['required', Rule::enum(ReadingStatus::class)],
        ]);
        
        $userScanProgress = UserScanProgress::create($validated);
        return redirect()->route('scan.index');
    }

    public function show(UserScanProgress $userScanProgress)
    {
        $this->authorize('view', $userScanProgress);
        $userScanProgress->load(['scan.genders']);
        $reading_status_list = ReadingStatus::all();
        return view('userScanProgress.show', compact('userScanProgress', 'reading_status_list'));
    }

    public function edit(UserScanProgress $userScanProgress)
    {
        $this->authorize('update', $userScanProgress);
        
        // Only store previous URL if it's not from the edit page
        $previousUrl = url()->previous();
        if (!str_contains($previousUrl, '/userScanProgress/' . $userScanProgress->id . '/edit')) {
            session(['userScanProgress_previous_url' => $previousUrl]);
        }
        
        $user = Auth::user();
        $reading_status_list = ReadingStatus::all();
        return view('userScanProgress.edit', compact('userScanProgress', 'user', 'reading_status_list'));
    }

    public function update(Request $request, UserScanProgress $userScanProgress)
    {
        $this->authorize('update', $userScanProgress);
        $validated = $request->validate([
            'current_chapter' => 'required|numeric|min:0',
            'reading_status' => ['required', Rule::enum(ReadingStatus::class)],
        ]);

        // Check if any values are actually different
        $hasChanges = false;
        foreach ($validated as $field => $value) {
            if ($userScanProgress->$field != $value) {
                $hasChanges = true;
                break;
            }
        }
        // If no changes were made
        if (!$hasChanges) {
            return redirect()->back()
                ->with('info', 'No changes were made to the scan progress.');
        }

        try{
            $userScanProgress->update($validated);
            return redirect()->back()->with('success', 'Scan updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update scan');
        }
    }

    public function destroy(UserScanProgress $userScanProgress)
    {
        $this->authorize('delete', $userScanProgress);
        $userScanProgress->delete();
        return redirect()->route('userScanProgress.index');
    }

    public function updateChapter(Request $request, UserScanProgress $userScanProgress)
    {
        $this->authorize('update', $userScanProgress);
        try {
            $validated = $request->validate([
                'current_chapter' => 'required|numeric|min:0',
            ]);

            $userScanProgress->update($validated);

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
