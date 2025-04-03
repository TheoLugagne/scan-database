<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserScanProgressController extends Controller
{
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
}
