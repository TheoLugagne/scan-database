<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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

    public function currentUserProgress()
    {
        return $this->hasOne(UserScanProgress::class)->where('user_id', Auth::id());
    }

    public function genres() {
        return $this->belongsToMany(Genre::class);
    }

    /**
     * True when title, summary, cover, link, status, available chapters, and at least one genre are filled.
     */
    protected function isInformationComplete(): Attribute
    {
        return Attribute::get(fn () => filled($this->title)
            && filled($this->summary)
            && filled($this->cover_image)
            && filled($this->link_to_scan)
            && filled($this->status)
            && $this->available_chapters !== null
            && ($this->relationLoaded('genres')
                ? $this->genres->isNotEmpty()
                : $this->genres()->exists()));
    }

    /**
     * Scans for which isInformationComplete is false: a blank required field, a null chapter count, or no genres.
     */
    public function scopeIncomplete(Builder $query): Builder
    {
        return $query->where(function (Builder $query) {
            foreach (['title', 'summary', 'cover_image', 'link_to_scan', 'status'] as $column) {
                $query->orWhere(fn (Builder $query) => $this->whereBlankColumn($query, $column));
            }

            $query->orWhereNull('available_chapters')
                ->orWhereDoesntHave('genres');
        });
    }

    /**
     * Null or whitespace-only values, matching filled().
     */
    private function whereBlankColumn(Builder $query, string $column): void
    {
        $wrapped = $query->getQuery()->getGrammar()->wrap($query->qualifyColumn($column));

        $query->whereNull($column)
            ->orWhereRaw("trim({$wrapped}) = ''");
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
