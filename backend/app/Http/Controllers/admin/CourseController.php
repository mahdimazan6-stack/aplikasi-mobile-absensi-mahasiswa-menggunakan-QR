<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(): View
    {
        return view('pages.admin.courses.index', [
            'courses' => Course::query()
                ->with(['lecturers:id,name', 'students:id,name'])
                ->withCount(['students', 'lecturers', 'attendanceSessions'])
                ->orderBy('name')
                ->paginate(25),
        ]);
    }

    public function create(): View
    {
        return view('pages.admin.courses.form', $this->formData(new Course));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateCourse($request);
        $course = Course::create($this->courseAttributes($validated));
        $this->syncMemberships($course, $validated);

        return to_route('admin.mata-kuliah.index')->with('success', 'Mata kuliah berhasil ditambahkan.');
    }

    public function edit(Course $course): View
    {
        return view('pages.admin.courses.form', $this->formData($course));
    }

    public function update(Request $request, Course $course): RedirectResponse
    {
        $validated = $this->validateCourse($request, $course);
        $course->update($this->courseAttributes($validated));
        $this->syncMemberships($course, $validated);

        return to_route('admin.mata-kuliah.index')->with('success', 'Mata kuliah berhasil diperbarui.');
    }

    public function destroy(Course $course): RedirectResponse
    {
        if ($course->attendanceSessions()->exists()) {
            return to_route('admin.mata-kuliah.index')
                ->with('error', 'Mata kuliah tidak dapat dihapus karena sudah memiliki sesi absensi.');
        }

        $course->delete();

        return to_route('admin.mata-kuliah.index')->with('success', 'Mata kuliah berhasil dihapus.');
    }

    private function formData(Course $course): array
    {
        return [
            'course' => $course->load(['lecturers:id,name', 'students:id,name']),
            'lecturers' => User::query()->where('role', 'dosen')->where('is_active', true)->orderBy('name')->get(['id', 'name', 'nim']),
            'students' => User::query()->where('role', 'mahasiswa')->where('is_active', true)->orderBy('name')->get(['id', 'name', 'nim']),
        ];
    }

    private function validateCourse(Request $request, ?Course $course = null): array
    {
        return $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('courses', 'code')->ignore($course?->id)],
            'name' => ['required', 'string', 'max:255'],
            'day' => ['required', 'string', 'max:20'],
            'time' => ['required', 'string', 'max:50'],
            'room' => ['required', 'string', 'max:100'],
            'kelas' => ['nullable', 'string', 'max:100'],
            'lecturer_ids' => ['required', 'array', 'min:1'],
            'lecturer_ids.*' => ['integer', Rule::exists('users', 'id')->where('role', 'dosen')->where('is_active', true)],
            'student_ids' => ['sometimes', 'array'],
            'student_ids.*' => ['integer', Rule::exists('users', 'id')->where('role', 'mahasiswa')->where('is_active', true)],
        ]);
    }

    private function courseAttributes(array $validated): array
    {
        unset($validated['lecturer_ids'], $validated['student_ids']);

        return $validated;
    }

    private function syncMemberships(Course $course, array $validated): void
    {
        $course->lecturers()->sync($validated['lecturer_ids']);
        $course->students()->sync($validated['student_ids'] ?? []);
    }
}
