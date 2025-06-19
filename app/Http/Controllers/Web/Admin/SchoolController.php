<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
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
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin.schools.index');
    }

    /**
     * Provide data for the DataTables AJAX request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable(Request $request)
    {
        if (!$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        $schools = School::with(['schoolAdmins', 'createdBy'])
            ->select(['id', 'address', 'phone', 'created_by', 'created_at']);

        return DataTables::of($schools)
            ->addIndexColumn()
            ->editColumn('created_at', fn ($school) => $school->created_at->format('d M Y'))
            ->addColumn('school_admin', fn ($school) =>
                $school->schoolAdmins->isEmpty() ? 'N/A' : $school->schoolAdmins->pluck('name')->implode(', ')
            )
            ->addColumn('school_email', fn ($school) =>
                $school->schoolAdmins->pluck('email')->implode(', ')
            )
            ->addColumn('action', fn ($school) =>
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
                'address' => $request->address,
                'phone' => $request->phone,
                'created_by' => Auth::id(),
            ]);

            $school->schoolAdmins()->attach($user->id);

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
            $school = School::whereHas('schoolAdmins', fn($query) => $query->where('slug', $slug))
                ->with(['schoolAdmins', 'createdBy'])
                ->firstOrFail();
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
            $school = School::whereHas('schoolAdmins', fn($query) => $query->where('slug', $slug))
                ->with(['schoolAdmins'])
                ->firstOrFail();
            $schoolAdmin = $school->schoolAdmins->first();
            return view('admin.schools.edit', compact('school', 'schoolAdmin'));
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
        $school = School::whereHas('schoolAdmins', fn($query) => $query->where('slug', $slug))
            ->with(['schoolAdmins'])
            ->firstOrFail();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . ($school->schoolAdmins->first()->id ?? 0)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        DB::beginTransaction();

        try {
            $schoolAdmin = $school->schoolAdmins->first();

            if (!$schoolAdmin) {
                throw new \Exception('No school admin found for this school.');
            }

            $schoolAdmin->update([
                'name' => $request->name,
                'email' => $request->email,
                'slug' => \Illuminate\Support\Str::slug($request->name . '-' . \Illuminate\Support\Str::random(5)),
                'password' => $request->filled('password') ? Hash::make($request->password) : $schoolAdmin->password,
            ]);

            $schoolAdmin->syncRoles('school_admin');

            $school->update([
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
            $school = School::whereHas('schoolAdmins', fn($query) => $query->where('slug', $slug))
                ->firstOrFail();

            if ($school->students()->count() > 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete school because it has associated students.'
                ], 422);
            }

            if ($school->medicalReports()->count() > 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete school because it has associated medical reports.'
                ], 422);
            }

            $school->schoolAdmins()->detach();
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
