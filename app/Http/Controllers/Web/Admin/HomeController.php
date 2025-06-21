<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\MedicalReport;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function redirectBasedOnRole()
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return $this->admin();
        } elseif ($user->hasRole('school_admin')) {
            return $this->adminDashboard();
        } else {
            abort(403, 'Unauthorized');
        }
    }

    // admin
    public function admin()
    {
        try {
            // Ensure user has admin role
            if (!Auth::user()->hasRole('admin')) {
                return redirect()->route('home')->with('error', 'Unauthorized access.');
            }

            // Fetch dashboard data
            $totalSchools = School::count();
            $totalStudents = Student::count();
            $totalMedicalReports = MedicalReport::count();

            // Data for pie chart (students by grade level)
            $gradeLevels = Student::select('grade_level')
                ->distinct()
                ->pluck('grade_level')
                ->toArray();
            $gradeLevelCounts = [];
            foreach ($gradeLevels as $grade) {
                $gradeLevelCounts[$grade] = Student::where('grade_level', $grade)->count();
            }

            // Data for bar chart (medical reports by status)
            $reportStatuses = MedicalReport::select('status')
                ->distinct()
                ->pluck('status')
                ->toArray();
            $statusCounts = [];
            foreach ($reportStatuses as $status) {
                $statusCounts[$status] = MedicalReport::where('status', $status)->count();
            }

            return view('admin.home', compact(
                'totalSchools',
                'totalStudents',
                'totalMedicalReports',
                'gradeLevelCounts',
                'statusCounts'
            ));
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }


    // school_admin
    public function adminDashboard()
    {
        try {
            // Ensure user has school_admin role
            if (!Auth::user()->hasRole('school_admin')) {
                return redirect()->route('admin.home')->with('error', 'Unauthorized access.');
            }

            // Fetch students for the authenticated admin
            $students = User::with('students')
                ->whereHas('students')
                ->where('user_id', Auth::id())
                ->get();

            // Prepare summary data
            $totalStudents = $students->count();
            $gradeLevels = $students->pluck('students.*.grade_level')
                ->flatten()
                ->filter()
                ->unique()
                ->count();
            $genderDistribution = $students->pluck('students.*.gender')
                ->flatten()
                ->groupBy(function ($gender) {
                    return $gender ?? 'N/A';
                })
                ->map->count()
                ->toArray();

            // Prepare student data for the table
            $studentData = $students->map(function ($user) {
                $student = $user->students->first();
                return [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $student->phone ?? 'N/A',
                    'grade_level' => $student->grade_level ?? 'N/A',
                    'gender' => $student->gender ?? 'N/A',
                    'action' => view('admin.students.action', ['student' => $user])->render(),
                ];
            });

            return view('admin.school_admin', compact(
                'totalStudents',
                'gradeLevels',
                'genderDistribution',
                'studentData'
            ));
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }
}
