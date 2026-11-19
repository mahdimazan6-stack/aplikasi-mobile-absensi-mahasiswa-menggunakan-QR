<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    protected $fillable = [
        'code',
        'name',
        'day',
        'time',
        'room',
        'kelas',
    ];

    public function attendanceSessions(): HasMany
    {
        return $this->hasMany(AttendanceSession::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'course_enrollments', 'course_id', 'student_id')
            ->withTimestamps();
    }

    public function lecturers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'course_lecturers', 'course_id', 'lecturer_id')
            ->withTimestamps();
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }
}
