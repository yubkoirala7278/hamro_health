<?php

namespace App\Http\Controllers\web\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.students.index');
    }


    /**
     * Provide data for the DataTables AJAX request.
     *
     */
    public function dataTable(Request $request)
    {
        if (!$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        if (Auth::user()->hasRole('admin')) {
            $students = User::with('students')->whereHas('students');
        } else {
            $students = User::with('students')
                ->whereHas('students')
                ->where("user_id", Auth::id());
        }

        return DataTables::of($students)
            ->addIndexColumn()
            ->editColumn('created_at', fn($user) => $user->created_at->format('d M Y'))
            ->addColumn('school_name', fn($user) => $user->user->name ?? 'N/A')
            ->addColumn('phone', fn($user) => optional($user->students->first())->phone ?? 'N/A')
            ->addColumn('dob', fn($user) => optional($user->students->first())->dob?->format('d M Y') ?? 'N/A')
            ->addColumn('gender', fn($user) => optional($user->students->first())->gender ?? 'N/A')
            ->addColumn('address', fn($user) => optional($user->students->first())->address ?? 'N/A')
            ->addColumn('parent_phone', fn($user) => optional($user->students->first())->parent_phone ?? 'N/A')
            ->addColumn('emergency_contact', fn($user) => optional($user->students->first())->emergency_contact ?? 'N/A')
            ->addColumn('grade_level', fn($user) => optional($user->students->first())->grade_level ?? 'N/A')
            ->addColumn('action', fn($user) => view('admin.students.action', ['student' => $user])->render())
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.students.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'dob' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'in:male,female,other'],
            'address' => ['nullable', 'string', 'max:255'],
            'parent_phone' => ['nullable', 'string', 'max:20'],
            'emergency_contact' => ['nullable', 'string', 'max:20'],
            'grade_level' => ['nullable', 'string', 'max:50'],
        ]);

        try {
            DB::beginTransaction();

            // Create the user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'slug' => Str::slug($validated['name'] . '-' . Str::random(6)), // Unique slug
                'user_id' => Auth::id(), // Set creator as the authenticated user
            ]);

            // Create the associated student record
            $user->students()->create([
                'user_id' => $user->id,
                'phone' => $validated['phone'] ?? null,
                'dob' => $validated['dob'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'address' => $validated['address'] ?? null,
                'parent_phone' => $validated['parent_phone'] ?? null,
                'emergency_contact' => $validated['emergency_contact'] ?? null,
                'grade_level' => $validated['grade_level'] ?? null,
            ]);

            DB::commit();

            return redirect()->route('admin.students.index')->with('success', 'Student created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->with('error', 'Something went wrong. Please try again. Error: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $student = User::with('student')->where('slug', $slug)->firstOrFail();
        return view('admin.students.show', compact('student'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $slug)
    {
        $student = User::with('student')->where('slug', $slug)->firstOrFail();
        return view('admin.students.edit', compact('student'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $slug)
    {
        $student = User::with('student')->where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $student->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'dob' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'in:male,female,other'],
            'address' => ['nullable', 'string', 'max:255'],
            'parent_phone' => ['nullable', 'string', 'max:20'],
            'emergency_contact' => ['nullable', 'string', 'max:20'],
            'grade_level' => ['nullable', 'string', 'max:50'],
        ]);

        try {
            DB::beginTransaction();

            // Update User
            $student->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => isset($validated['password']) ? Hash::make($validated['password']) : $student->password,
                'slug' => $student->slug, // Preserve existing slug
            ]);

            // Update or Create the student record
            $student->students()->updateOrCreate(
                ['user_id' => $student->id],
                [
                    'phone' => $validated['phone'] ?? null,
                    'dob' => $validated['dob'] ?? null,
                    'gender' => $validated['gender'] ?? null,
                    'address' => $validated['address'] ?? null,
                    'parent_phone' => $validated['parent_phone'] ?? null,
                    'emergency_contact' => $validated['emergency_contact'] ?? null,
                    'grade_level' => $validated['grade_level'] ?? null,
                ]
            );

            DB::commit();

            return redirect()->route('admin.students.index')->with('success', 'Student updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Something went wrong. ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($slug)
    {
        $user = User::where('slug', $slug)->firstOrFail();
        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Student deleted successfully.'
        ]);
    }
}
