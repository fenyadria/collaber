<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'category_id',
        'name',
        'description',
        'goal',
        'max_members',
        'status',
        'deadline',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'date',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'project_skills')
            ->withTimestamps();
    }

    public function applications(): HasMany
    {
        return $this->hasMany(ProjectApplication::class);
    }
}