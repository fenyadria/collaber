<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProjectTask extends Model
{
    use HasFactory;

    protected $table = 'project_tasks';

    protected $attributes = ['status' => 'todo'];

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'status',
        'deadline',
        'submission_path',
        'submission_note',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'date',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assignees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_assignments', 'project_task_id', 'user_id')
            ->withTimestamps();
    }
}