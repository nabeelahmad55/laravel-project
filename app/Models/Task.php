<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'project_id',
        'assigned_to',
        'created_by',
        'priority',
        'status',
        'position',
        'due_date',
        'completed_at'
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'datetime'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class);
    }

    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class);
    }

    // Scope for filtering
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['status'] ?? null, function ($query, $status) {
            $query->where('status', $status);
        })->when($filters['priority'] ?? null, function ($query, $priority) {
            $query->where('priority', $priority);
        })->when($filters['assigned_to'] ?? null, function ($query, $userId) {
            $query->where('assigned_to', $userId);
        });
    }
}