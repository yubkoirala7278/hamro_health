<?php

namespace App\Http\Controllers\web\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StudentProfile;
use App\Models\School;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Hash;
use Throwable;
use Exception;

class StudentController extends Controller
{
    /**
     * Display the student management index view.
     */
    public function index()
    {

        return view('admin.students.index');
    }

    /**
     * Provide data for DataTables AJAX request.
     */
    public function dataTable(Request $request)
    {
        if (!$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        $users = User::with(['roles', 'studentProfile.school'])
            ->whereHas('roles', fn($query) => $query->where('name', 'student'));

        return DataTables::of($users)
            ->addIndexColumn()
            ->editColumn('created_at', fn($user) => $user->created_at->format('d M Y'))
            ->addColumn('roles', fn($user) => $user->roles->pluck('name')->implode(', '))
            ->addColumn('school', fn($user) => $user->studentProfile?->school?->name ?? 'N/A')
            ->addColumn('grade_level', fn($user) => $user->studentProfile->grade_level ?? 'N/A')
            ->addColumn('action', fn($user) => view('admin.students.action', ['user' => $user])->render())
            ->rawColumns(['action'])
            ->make(true);
    }


    /**
     * Show form for creating a new student.
     */
    public function create()
    {
        return view('admin.students.create');
    }

    /**
     * Store a newly created student.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'            => ['required', 'string', 'max:255'],
            'email'           => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'        => ['required', 'string', 'min:8', 'confirmed'],
            'school_id'       => ['required', 'exists:schools,id'],
            'phone'           => ['nullable', 'string', 'max:20'],
            'dob'             => ['required', 'date', 'before:today'],
            'gender'          => ['required', 'in:male,female,other'],
            'address'         => ['nullable', 'string', 'max:255'],
            'parent_phone'    => ['nullable', 'string', 'max:20'],
            'emergency_contact' => ['nullable', 'string', 'max:255'],
            'grade_level'     => ['required', 'string', 'max:50'],
        ]);

        try {
            // Create user with student role
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'slug'     => \Illuminate\Support\Str::slug($request->name . '-' . \Illuminate\Support\Str::random(5)),
            ]);
            $user->assignRole('student');

            // Create related student profile
            StudentProfile::create([
                'user_id'          => $user->id,
                'school_id'        => $request->school_id,
                'phone'            => $request->phone,
                'dob'              => $request->dob,
                'gender'           => $request->gender,
                'address'          => $request->address,
                'parent_phone'     => $request->parent_phone,
                'emergency_contact' => $request->emergency_contact,
                'grade_level'      => $request->grade_level,
            ]);

            return redirect()->route('admin.students.index')->with('success', 'Student created successfully.');
        } catch (Throwable $th) {
            return back()->withInput()->with('error', 'Failed to create student: ' . $th->getMessage());
        }
    }

    /**
     * Display specified student.
     */
    public function show($slug)
    {
        try {
            $user = User::with('studentProfile.school')->where('slug', $slug)->firstOrFail();
            return view('admin.students.show', compact('user'));
        } catch (Exception $e) {
            return back()->with('error', 'Student not found.');
        }
    }

    /**
     * Show form for editing specified student.
     */
    public function edit($slug)
    {
        try {
             $user = User::with('studentProfile.school')->where('slug', $slug)->firstOrFail();
            return view('admin.students.edit', compact('user'));
        } catch (Exception $e) {
            return back()->with('error', 'Student not found.');
        }
    }

    /**
     * Update the specified student.
     */
    public function update(Request $request, $slug)
    {
        try {
            $user = User::with('studentProfile')->where('slug', $slug)->firstOrFail();

            $request->validate([
                'name'            => ['required', 'string', 'max:255'],
                'email'           => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
                'password'        => ['nullable', 'string', 'min:8', 'confirmed'],
                'school_id'       => ['required', 'exists:schools,id'],
                'phone'           => ['nullable', 'string', 'max:20'],
                'dob'             => ['required', 'date', 'before:today'],
                'gender'          => ['required', 'in:male,female,other'],
                'address'         => ['nullable', 'string', 'max:255'],
                'parent_phone'    => ['nullable', 'string', 'max:20'],
                'emergency_contact' => ['nullable', 'string', 'max:255'],
                'grade_level'     => ['required', 'string', 'max:50'],
            ]);

            $user->update([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => $request->filled('password') ? Hash::make($request->password) : $user->password,
            ]);

            $user->syncRoles('student');

            $user->studentProfile->update([
                'school_id'        => $request->school_id,
                'phone'            => $request->phone,
                'dob'              => $request->dob,
                'gender'           => $request->gender,
                'address'          => $request->address,
                'parent_phone'     => $request->parent_phone,
                'emergency_contact' => $request->emergency_contact,
                'grade_level'      => $request->grade_level,
            ]);

            return redirect()->route('admin.students.index')->with('success', 'Student updated successfully.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Student not found.');
        } catch (Throwable $th) {
            return back()->withInput()->with('error', 'Failed to update student: ' . $th->getMessage());
        }
    }

    /**
     * Remove the specified student.
     */
    public function destroy($slug)
    {
        try {
            $user = User::where('slug', $slug)->firstOrFail();

            if ($user->medicalReports()->count() > 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete student because they have associated medical reports.'
                ], 422);
            }

            $user->studentProfile()->delete();
            $user->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Student deleted successfully.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Student not found.'
            ], 404);
        } catch (Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete student: ' . $th->getMessage()
            ], 500);
        }
    }
}
