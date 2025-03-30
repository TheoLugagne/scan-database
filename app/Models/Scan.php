<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scan extends Model
{
    /** @use HasFactory<\Database\Factories\ScanFactory> */
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'summary',
        'current_chapter',
        'cover_image',
        'link_to_scan',
        'create_date',
        'last_update',
        'user_id'
    ];

    protected $casts = [
        'create_date' => 'datetime',
        'last_update' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
