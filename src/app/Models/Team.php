<?php

namespace App\Models;

use Dom\Attr;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Laravel\Jetstream\Events\TeamCreated;
use Laravel\Jetstream\Events\TeamDeleted;
use Laravel\Jetstream\Events\TeamUpdated;
use Laravel\Jetstream\Team as JetstreamTeam;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Team extends JetstreamTeam implements AuditableContract
{
    use HasFactory, Auditable;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'personal_team' => 'boolean',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'personal_team',
        'locale',
    ];

    /**
     * The event map for the model.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => TeamCreated::class,
        'updated' => TeamUpdated::class,
        'deleted' => TeamDeleted::class,
    ];

    protected static function booted(): void
    {
        static::creating(function ($team) {
            if (empty($team->slug)) {
                $team->slug = $team->factorySlug();
            }
        });

        static::updating(function ($team) {
            if ($team->isDirty('name') || empty($team->slug)) {
                $team->slug = $team->factorySlug();
            }
        });
    }

    public function slugIsUnique(string $slug): bool
    {
        $query = static::where('slug', $slug);

        if ($this->exists) {
            $query->where('id', '!=', $this->id);
        }

        return !$query->exists();
    }

    public function factorySlug(): string
    {
        $maxLength = 255;
        $base = Str::slug($this->name ?: 'team');
        $base = Str::limit($base, $maxLength, '');

        $slug = $base;
        $i = 1;

        while (!$this->slugIsUnique($slug)) {
            $suffix = '-' . $i++;
            $allowed = $maxLength - strlen($suffix);
            $slug = Str::limit($base, max(1, $allowed), '') . $suffix;
        }

        return $slug;
    }

    /**
     * Get the locale for this team.
     * Falls back to app default if not set.
     */
    public function locale($value): Attribute
    {
        return new Attribute(
            get: fn(): string => $value,
            set: fn($value): string => $value ?? config('app.locale', 'pt_BR'),
        );
    }
}
