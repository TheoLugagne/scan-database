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
    ];

    public function userScanProgress()
    {
        return $this->hasMany(UserScanProgress::class);
    }

    public function genders() {
        return $this->belongsToMany(Gender::class);
    }
}
