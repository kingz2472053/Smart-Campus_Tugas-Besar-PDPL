<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('lecturer.user')->latest()->paginate(10);
        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {
        return view('admin.courses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'academic_year' => 'required|string|max:50',
            'code' => 'required|string|max:20',
            'name' => 'required|string|max:255',
            'sks' => 'required|integer',
            'semester' => 'required|string',
            'classes' => 'required|array|min:1',
            'classes.*.class_name' => 'required|string|max:10',
            'classes.*.lecturer_id' => 'required|exists:lecturers,id',
            'classes.*.kuota' => 'required|integer',
        ]);

        foreach ($request->classes as $class) {
            // Cek apakah kombinasi unik sudah ada
            $exists = Course::where('code', $request->code)
                            ->where('class_name', $class['class_name'])
                            ->where('academic_year', $request->academic_year)
                            ->exists();

            if ($exists) {
                return back()->with('error', "Kelas {$class['class_name']} untuk Mata Kuliah {$request->code} di Tahun Ajaran {$request->academic_year} sudah ada.")->withInput();
            }

            Course::create([
                'academic_year' => $request->academic_year,
                'code' => $request->code,
                'name' => $request->name,
                'sks' => $request->sks,
                'semester' => $request->semester,
                'class_name' => $class['class_name'],
                'lecturer_id' => $class['lecturer_id'],
                'kuota' => $class['kuota'],
            ]);
        }

        return redirect()->route('admin.courses.index')->with('success', 'Mata Kuliah beserta kelasnya berhasil dibuat.');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(Course $course)
    {
        return view('admin.courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        $request->validate([
            'code' => 'required|string|max:20|unique:courses,code,' . $course->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $course->update($request->all());

        return redirect()->route('admin.courses.index')->with('success', 'Mata Kuliah berhasil diperbarui.');
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return redirect()->route('admin.courses.index')->with('success', 'Mata Kuliah berhasil dihapus.');
    }
}
