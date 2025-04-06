<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\UserScanProgress;    
use App\Models\Scan;
use Illuminate\Support\Facades\Auth;

class UserScanProgressController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', UserScanProgress::class);
        $perPage = $request->input('per_page', 12);
        
        // Get all scans and paginate them
        $userScanProgress = UserScanProgress::latest('updated_at')
            ->paginate($perPage);
            
        return view('userScanProgress.index', compact('userScanProgress', 'perPage'));
    }

    public function fetch(Request $request)
    {
        // Clean and validate the per_page parameter
        $perPage = (int) $request->input('per_page', 12);
        // Ensure it's at least 1
        $perPage = max(1, $perPage);

        $search = $request->input('search', '');

        // Use the UserScanProgress model directly with a query builder
        $query = UserScanProgress::query()
            ->where('user_id', Auth::id())
            ->join('scans', 'user_scan_progress.scan_id', '=', 'scans.id');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('scans.title', 'like', '%' . $search . '%')
                  ->orWhere('scans.link_to_scan', 'like', '%' . $search . '%');
            });
        }

        $scans = $query->latest('scans.updated_at')
            ->select('user_scan_progress.*')
            ->paginate($perPage);

        return view('scan.partials.scan-list', compact('scans'))->render();
    }

    public function create(Scan $scan)
    {
        $this->authorize('create', UserScanProgress::class);
        return view('userScanProgress.create', compact('scan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'scan_id' => 'required|exists:scans,id',
            'current_chapter' => 'required|numeric|min:0',
        ]);
        
        UserScanProgress::create($validated);
        return true;
    }

    public function show(UserScanProgress $userScanProgress)
    {
        $this->authorize('view', $userScanProgress);
        return view('userScanProgress.show', compact('userScanProgress'));
    }

    public function edit(UserScanProgress $userScanProgress)
    {
        $this->authorize('update', $userScanProgress);
        return view('userScanProgress.edit', compact('userScanProgress'));
    }

    public function update(Request $request, UserScanProgress $userScanProgress)
    {
        $this->authorize('update', $userScanProgress);
        $validated = $request->validate([
            'current_chapter' => 'required|numeric|min:0',
        ]);
        $userScanProgress->update($validated);
        return redirect()->route('userScanProgress.index');
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
