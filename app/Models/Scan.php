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
        'available_chapters',
        'available_chapters_updated_at',
    ];

    protected $casts = [
        'status' => ScanStatus::class,
        'available_chapters' => 'integer',
        'available_chapters_updated_at' => 'datetime',
    ];

    public function userScanProgress()
    {
        return $this->hasMany(UserScanProgress::class);
    }

    public function genres() {
        return $this->belongsToMany(Genre::class);
    }

    public static function getFilterSectionsData() {
        return [
            'status' => [
                'title' => 'Status',
                'elts' => ScanStatus::all(),
                'type' => 'radio',
                'name' => 'status',
            ],
            'genres' => [
                'title' => 'Genres',
                'elts' => Genre::all(),
                'type' => 'multiselect',
                'name' => 'genre_ids',
            ],
        ];
    }
}
