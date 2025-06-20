<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Throwable;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SchoolController extends Controller
{
    /**
     * Display the school management index view.
     *
     */
    public function index()
    {
        return view('admin.schools.index');
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

        $schools = School::with('admin');

        return DataTables::of($schools)
            ->addIndexColumn()
            ->editColumn('created_at', fn($school) => $school->created_at->format('d M Y'))
            ->addColumn(
                'school_admin',
                fn($school) =>
                optional($school->admin)->name ?? 'N/A'
            )
            ->addColumn(
                'school_email',
                fn($school) =>
                optional($school->admin)->email ?? 'N/A'
            )
            ->addColumn(
                'action',
                fn($school) =>
                view('admin.schools.action', ['school' => $school])->render()
            )
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new school and school admin.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.schools.create');
    }

    /**
     * Store a newly created school and school admin in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'slug' => \Illuminate\Support\Str::slug($request->name . '-' . \Illuminate\Support\Str::random(5)),
            ]);

            $user->assignRole('school_admin');

            $school = School::create([
                'user_id' => $user->id,
                'address' => $request->address,
                'phone' => $request->phone,
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('admin.schools.index')->with('success', 'School and admin created successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', 'Failed to create school: ' . $th->getMessage());
        }
    }


    /**
     * Display the specified school.
     *
     * @param string $slug
     * @return \Illuminate\View\View
     */
    public function show($slug)
    {
        try {
            $school = User::with('school')->where('slug', $slug)->firstOrFail();
            return view('admin.schools.show', compact('school'));
        } catch (\Exception $e) {
            return back()->with('error', 'School not found.');
        }
    }


    /**
     * Show the form for editing the specified school.
     *
     * @param string $slug
     * @return \Illuminate\View\View
     */
    public function edit($slug)
    {
        try {
            $school = User::with('school')->where('slug', $slug)->firstOrFail();
            return view('admin.schools.edit', compact('school'));
        } catch (\Exception $e) {
            return back()->with('error', 'School not found.');
        }
    }

    /**
     * Update the specified school in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $slug
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $slug)
    {
        $user = User::with('school')->where('slug', $slug)->firstOrFail();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        DB::beginTransaction();

        try {
            // Update User (admin) fields
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password ? Hash::make($request->password) : $user->password,
            ]);

            // Update School fields
            $user->school->update([
                'address' => $request->address,
                'phone' => $request->phone,
            ]);

            DB::commit();

            return redirect()->route('admin.schools.index')->with('success', 'School updated successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', 'Failed to update school: ' . $th->getMessage());
        }
    }


    /**
     * Remove the specified school from storage.
     *
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($slug)
    {
        try {
            $school = User::where('slug', $slug)->first();
            $students = User::where('user_id',$school->id)->get();

            // Check if the school has students
            if ($students->isNotEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete school because it has associated students.'
                ], 422);
            }

            // Delete the school
            $school->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'School deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'School not found.'
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete school: ' . $th->getMessage()
            ], 500);
        }
    }
}
