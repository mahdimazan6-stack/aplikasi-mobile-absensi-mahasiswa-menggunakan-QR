<?php

namespace Tests\Feature;

use App\Models\AttendanceSession;
use App\Models\Course;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BackendAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_web_routes_require_authentication_and_admin_role(): void
    {
        $this->get('/admin/dashboard')->assertRedirect('/login');

        $student = User::factory()->create(['role' => 'mahasiswa']);

        $this->actingAs($student)
            ->get('/admin/dashboard')
            ->assertForbidden();
    }

    public function test_admin_can_login_and_is_redirected_to_admin_dashboard(): void
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('correct-password'),
            'role' => 'admin',
        ]);

        $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'correct-password',
        ])
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHasNoErrors();

        $this->assertAuthenticated();
    }

    public function test_student_can_login_to_the_web_dashboard_with_nim(): void
    {
        User::factory()->create([
            'email' => 'student@example.com',
            'nim' => '3312511003',
            'password' => Hash::make('correct-password'),
            'role' => 'mahasiswa',
        ]);

        $this->post('/login', [
            'email' => '3312511003',
            'password' => 'correct-password',
        ])
            ->assertRedirect(route('user.dashboard'))
            ->assertSessionHasNoErrors();

        $this->assertAuthenticated();
    }

    public function test_admin_can_manage_accounts_and_course_rosters(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $lecturer = User::factory()->create(['role' => 'dosen']);
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $this->assertTrue($admin->fresh()->is_active);
        $this->assertSame('admin', $admin->fresh()->role);
        $this->actingAs($admin)->get('/admin/pengguna')->assertOk();
        $this->actingAs($admin)->get('/admin/dashboard')->assertOk();
        $this->actingAs($admin)->get('/admin/kehadiran')->assertOk();
        $this->actingAs($admin)->get('/admin/izin')->assertOk();
        $this->actingAs($admin)->get('/admin/mata-kuliah')->assertOk();
        $this->actingAs($admin)->get('/admin/mata-kuliah/create')->assertOk();

        $this->actingAs($admin)
            ->post('/admin/pengguna', [
                'name' => 'Mahasiswa Baru',
                'email' => 'baru@example.com',
                'nim' => '3312512000',
                'role' => 'mahasiswa',
                'password' => 'secure-pass-123',
                'password_confirmation' => 'secure-pass-123',
            ])
            ->assertRedirect(route('admin.pengguna.index'));
        $this->assertDatabaseHas('users', ['nim' => '3312512000', 'role' => 'mahasiswa', 'is_active' => 1]);

        $this->actingAs($admin)
            ->post('/admin/mata-kuliah', [
                'code' => 'IF201',
                'name' => 'Algoritma',
                'day' => 'Senin',
                'time' => '08:00 - 10:00',
                'room' => 'B201',
                'lecturer_ids' => [$lecturer->id],
                'student_ids' => [$student->id],
            ])
            ->assertRedirect(route('admin.mata-kuliah.index'));

        $course = Course::where('code', 'IF201')->firstOrFail();
        $this->assertTrue($course->lecturers()->whereKey($lecturer->id)->exists());
        $this->assertTrue($course->students()->whereKey($student->id)->exists());
        $this->actingAs($admin)->get(route('admin.mata-kuliah.edit', $course))->assertOk();
    }

    public function test_login_rejects_an_incorrect_password(): void
    {
        User::factory()->create([
            'email' => 'student@example.com',
            'password' => Hash::make('correct-password'),
            'role' => 'mahasiswa',
        ]);

        $this->from('/login')
            ->post('/login', [
                'email' => 'student@example.com',
                'password' => 'wrong-password',
            ])
            ->assertRedirect('/login')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_only_the_lecturer_who_created_a_session_can_end_it(): void
    {
        $lecturer = User::factory()->create(['role' => 'dosen']);
        $otherLecturer = User::factory()->create(['role' => 'dosen']);
        $course = Course::create([
            'code' => 'IF101',
            'name' => 'Pemrograman',
            'day' => 'Senin',
            'time' => '08:00 - 10:00',
            'room' => 'A101',
        ]);
        $session = AttendanceSession::create([
            'course_id' => $course->id,
            'lecturer_id' => $lecturer->id,
            'qr_token' => str_repeat('a', 64),
            'expires_at' => now()->addMinutes(5),
        ]);
        $token = 'other-lecturer-token';
        $otherLecturer->update(['api_token' => hash('sha256', $token)]);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson("/api/attendance/sessions/{$session->id}/end")
            ->assertForbidden();

        $this->assertNull($session->fresh()->ended_at);
    }

    public function test_student_can_scan_a_qr_code_once_and_view_their_history(): void
    {
        $student = User::factory()->create([
            'nim' => '3312511001',
            'password' => Hash::make('student-password'),
            'role' => 'mahasiswa',
        ]);
        $lecturer = User::factory()->create(['role' => 'dosen']);
        $course = Course::create([
            'code' => 'IF102',
            'name' => 'Basis Data',
            'day' => 'Selasa',
            'time' => '10:00 - 12:00',
            'room' => 'A102',
        ]);
        $session = AttendanceSession::create([
            'course_id' => $course->id,
            'lecturer_id' => $lecturer->id,
            'qr_token' => str_repeat('b', 64),
            'expires_at' => now()->addMinutes(5),
        ]);
        $course->students()->attach($student->id);
        $session->students()->attach($student->id);

        $token = $this->postJson('/api/login', [
            'nim' => '3312511001',
            'password' => 'student-password',
        ])
            ->assertOk()
            ->json('token');

        $this->withToken($token)
            ->postJson('/api/attendance/scan', ['qr_token' => $session->qr_token])
            ->assertCreated();

        $this->postJson('/api/attendance/scan', ['qr_token' => $session->qr_token])
            ->assertStatus(409);

        $this->getJson('/api/attendance/history')
            ->assertOk()
            ->assertJsonCount(1, 'history');
    }

    public function test_a_student_outside_the_session_roster_cannot_scan_its_qr(): void
    {
        $student = User::factory()->create([
            'nim' => '3312511002',
            'password' => Hash::make('student-password'),
            'role' => 'mahasiswa',
        ]);
        $lecturer = User::factory()->create(['role' => 'dosen']);
        $course = Course::create([
            'code' => 'IF103',
            'name' => 'Jaringan',
            'day' => 'Rabu',
            'time' => '13:00 - 15:00',
            'room' => 'A103',
        ]);
        $session = AttendanceSession::create([
            'course_id' => $course->id,
            'lecturer_id' => $lecturer->id,
            'qr_token' => str_repeat('c', 64),
            'expires_at' => now()->addMinutes(5),
        ]);
        $token = 'unenrolled-student-token';
        $student->update(['api_token' => hash('sha256', $token)]);

        $this->withToken($token)
            ->postJson('/api/attendance/scan', ['qr_token' => $session->qr_token])
            ->assertForbidden();
    }

    public function test_lecturer_can_only_start_a_session_for_an_assigned_course_with_a_roster(): void
    {
        $lecturer = User::factory()->create(['role' => 'dosen']);
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $course = Course::create([
            'code' => 'IF106',
            'name' => 'Keamanan Informasi',
            'day' => 'Sabtu',
            'time' => '13:00 - 15:00',
            'room' => 'A106',
        ]);
        $course->lecturers()->attach($lecturer->id);
        $course->students()->attach($student->id);
        $token = 'assigned-lecturer-token';
        $lecturer->update(['api_token' => hash('sha256', $token)]);

        $sessionId = $this->withToken($token)
            ->postJson('/api/attendance/sessions', ['course_id' => $course->id])
            ->assertCreated()
            ->json('session_id');

        $this->assertDatabaseHas('attendance_session_students', [
            'attendance_session_id' => $sessionId,
            'student_id' => $student->id,
        ]);
    }

    public function test_closing_a_session_marks_unscanned_roster_as_absent_or_excused(): void
    {
        $lecturer = User::factory()->create(['role' => 'dosen']);
        $presentStudent = User::factory()->create(['role' => 'mahasiswa']);
        $excusedStudent = User::factory()->create(['role' => 'mahasiswa']);
        $course = Course::create([
            'code' => 'IF104',
            'name' => 'Sistem Operasi',
            'day' => 'Kamis',
            'time' => '09:00 - 11:00',
            'room' => 'A104',
        ]);
        $session = AttendanceSession::create([
            'course_id' => $course->id,
            'lecturer_id' => $lecturer->id,
            'qr_token' => str_repeat('d', 64),
            'expires_at' => now()->addMinutes(5),
        ]);
        $session->students()->attach([$presentStudent->id, $excusedStudent->id]);
        $session->attendances()->create([
            'student_id' => $presentStudent->id,
            'status' => 'hadir',
            'attended_at' => now(),
        ]);
        LeaveRequest::create([
            'student_id' => $excusedStudent->id,
            'course_id' => $course->id,
            'request_date' => $session->created_at->toDateString(),
            'reason' => 'Sakit',
            'status' => 'approved',
        ]);
        $token = 'session-lecturer-token';
        $lecturer->update(['api_token' => hash('sha256', $token)]);

        $this->withToken($token)
            ->postJson("/api/attendance/sessions/{$session->id}/end")
            ->assertOk();

        $this->assertDatabaseHas('attendances', [
            'attendance_session_id' => $session->id,
            'student_id' => $presentStudent->id,
            'status' => 'hadir',
        ]);
        $this->assertDatabaseHas('attendances', [
            'attendance_session_id' => $session->id,
            'student_id' => $excusedStudent->id,
            'status' => 'izin',
            'attended_at' => null,
        ]);
        $this->assertTrue($session->fresh()->ended_at !== null);
    }

    public function test_student_can_submit_leave_request_and_admin_can_approve_it(): void
    {
        Storage::fake('local');
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $admin = User::factory()->create(['role' => 'admin']);
        $lecturer = User::factory()->create(['role' => 'dosen']);
        $course = Course::create([
            'code' => 'IF105',
            'name' => 'Basis Data',
            'day' => 'Jumat',
            'time' => '10:00 - 12:00',
            'room' => 'A105',
        ]);
        $course->students()->attach($student->id);
        $session = AttendanceSession::create([
            'course_id' => $course->id,
            'lecturer_id' => $lecturer->id,
            'qr_token' => str_repeat('e', 64),
            'expires_at' => now(),
            'ended_at' => now(),
        ]);
        $session->students()->attach($student->id);
        $session->attendances()->create([
            'student_id' => $student->id,
            'status' => 'alpa',
            'attended_at' => null,
        ]);

        $this->actingAs($student)->get('/user/dashboard')->assertOk();
        $this->actingAs($student)->get('/user/laporan')->assertOk();
        $this->actingAs($student)->get('/user/izin')->assertOk();

        $this->actingAs($student)
            ->post('/user/izin', [
                'course_id' => $course->id,
                'request_date' => $session->created_at->toDateString(),
                'reason' => 'Sakit',
                'file' => UploadedFile::fake()->create('surat.pdf', 100, 'application/pdf'),
            ])
            ->assertRedirect(route('user.izin'));

        $leaveRequest = LeaveRequest::firstOrFail();
        Storage::disk('local')->assertExists($leaveRequest->attachment_path);

        $this->actingAs($admin)
            ->post(route('admin.izin.approve', $leaveRequest))
            ->assertRedirect();

        $this->assertDatabaseHas('leave_requests', [
            'id' => $leaveRequest->id,
            'status' => 'approved',
            'reviewed_by' => $admin->id,
        ]);
        $this->assertDatabaseHas('attendances', [
            'attendance_session_id' => $session->id,
            'student_id' => $student->id,
            'status' => 'izin',
        ]);

        $this->actingAs(User::factory()->create(['role' => 'mahasiswa']))
            ->get(route('user.izin.attachment', $leaveRequest))
            ->assertNotFound();
    }
}
