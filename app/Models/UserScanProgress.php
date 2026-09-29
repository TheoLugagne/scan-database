<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserScanProgress extends Model
{
    protected $fillable = [
        'user_id',
        'scan_id',
        'current_chapter',
        'reading_status',
    ];

    protected $casts = [
        'reading_status' => ReadingStatus::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scan() 
    {
        return $this->belongsTo(Scan::class);
    }

    public static function getFilterSectionsData() {
        return [
            'reading_status' => [
                'title' => 'Reading Status',
                'elts' => ReadingStatus::all(),
                'type' => 'radio',
                'name' => 'reading_status',
            ],
        ];
    }
}
