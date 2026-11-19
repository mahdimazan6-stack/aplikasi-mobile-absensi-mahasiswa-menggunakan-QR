<?php

namespace Tests\Feature;

use App\Models\AttendanceSession;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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
}
