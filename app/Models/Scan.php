<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scan extends Model
{
    /** @use HasFactory<\Database\Factories\ScanFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'summary',
        'cover_image',
        'link_to_scan',
        'status',
    ];

    protected $casts = [
        'status' => ScanStatus::class,
    ];

    public function userScanProgress()
    {
        return $this->hasMany(UserScanProgress::class);
    }

    public function genders() {
        return $this->belongsToMany(Gender::class);
    }

    public static function getFilterSectionsData() {
        return [
            'status' => [
                'title' => 'Status',
                'elts' => ScanStatus::all(),
                'type' => 'radio',
                'name' => 'status',
            ],
            'genders' => [
                'title' => 'Genders',
                'elts' => Gender::all(),
                'type' => 'multiselect',
                'name' => 'gender_ids',
            ],
        ];
    }
}
