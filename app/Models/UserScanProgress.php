<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserScanProgress extends Model
{
    protected $fillable = [
        'user_id',
        'scan_id',
        'current_chapter',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scan() 
    {
        return $this->belongsTo(Scan::class);
    }
}
