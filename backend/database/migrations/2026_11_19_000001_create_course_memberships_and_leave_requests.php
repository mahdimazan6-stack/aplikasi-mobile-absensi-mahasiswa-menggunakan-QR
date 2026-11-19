<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'is_active')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('role');
            });
        }

        Schema::table('attendances', function (Blueprint $table) {
            $table->timestamp('attended_at')->nullable()->change();
        });

        if (! Schema::hasTable('course_enrollments')) {
            Schema::create('course_enrollments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained()->cascadeOnDelete();
                $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
                $table->timestamps();
            });
        }
        $this->ensureUniqueIndex('course_enrollments', ['course_id', 'student_id'], 'course_enrollment_unique');

        if (! Schema::hasTable('course_lecturers')) {
            Schema::create('course_lecturers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained()->cascadeOnDelete();
                $table->foreignId('lecturer_id')->constrained('users')->cascadeOnDelete();
                $table->timestamps();
            });
        }
        $this->ensureUniqueIndex('course_lecturers', ['course_id', 'lecturer_id'], 'course_lecturer_unique');

        if (! Schema::hasTable('attendance_session_students')) {
            Schema::create('attendance_session_students', function (Blueprint $table) {
                $table->id();
                $table->foreignId('attendance_session_id')->constrained()->cascadeOnDelete();
                $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
                $table->timestamps();
            });
        }
        $this->ensureUniqueIndex(
            'attendance_session_students',
            ['attendance_session_id', 'student_id'],
            'session_student_unique'
        );

        if (! Schema::hasTable('leave_requests')) {
            Schema::create('leave_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('course_id')->constrained()->cascadeOnDelete();
                $table->date('request_date');
                $table->text('reason');
                $table->string('attachment_path')->nullable();
                $table->string('status')->default('pending');
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('reviewed_at')->nullable();
                $table->timestamps();
                $table->index(['course_id', 'request_date', 'status']);
                $table->index(['student_id', 'request_date']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
        Schema::dropIfExists('attendance_session_students');
        Schema::dropIfExists('course_lecturers');
        Schema::dropIfExists('course_enrollments');

        if (Schema::hasColumn('users', 'is_active')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }
    }

    private function ensureUniqueIndex(string $tableName, array $columns, string $indexName): void
    {
        $hasIndex = collect(Schema::getIndexes($tableName))
            ->contains(fn (array $index): bool => $index['type'] === 'unique' && $index['columns'] === $columns);

        if (! $hasIndex) {
            Schema::table($tableName, function (Blueprint $table) use ($columns, $indexName) {
                $table->unique($columns, $indexName);
            });
        }
    }
};
