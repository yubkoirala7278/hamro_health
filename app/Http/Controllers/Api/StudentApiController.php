<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\MedicalReport;
use Illuminate\Support\Str;
use App\Models\Message;

class StudentApiController extends Controller
{
    /**
     * Get authenticated student's medical reports.
     */
    public function getMedicalReports()
    {
        $user = Auth::user();

        // Ensure the user is authenticated and has the 'student' role
        if (!$user || !$user->hasRole('student')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Find the student profile associated with the user
        $student = Student::where('user_id', $user->id)->first();
        if (!$student) {
            return response()->json(['error' => 'Student profile not found'], 404);
        }

        // Retrieve the student's medical reports
        $medicalReports = MedicalReport::where('student_id', $student->id)
            ->select([
                'report_date',
                'medical_condition',
                'allergies',
                'medications',
                'vaccinations',
                'doctor_name',
                'status',
                'created_at'
            ])
            ->orderBy('report_date', 'desc')
            ->get();

        // Return the reports in a JSON response
        return response()->json([
            'status' => 'success',
            'data' => $medicalReports
        ], 200);
    }


    /**
     * Get authenticated student's profile.
     */
    public function getProfile()
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole('student')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Fetch student profile with related user data
        $student = Student::where('user_id', $user->id)->first();
        if (!$student) {
            return response()->json(['error' => 'Student profile not found'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
                'phone' => $student->phone,
                'dob' => $student->dob ? $student->dob->format('Y-m-d') : null,
                'gender' => $student->gender,
                'address' => $student->address,
                'parent_phone' => $student->parent_phone,
                'emergency_contact' => $student->emergency_contact,
                'grade_level' => $student->grade_level,
                'school_name' => $user->creator ? $user->creator->name : null,
            ]
        ], 200);
    }

    /**
     * Get recent updates (new medical reports or messages).
     */
    public function getRecentUpdates()
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole('student')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $student = Student::where('user_id', $user->id)->first();
        if (!$student) {
            return response()->json(['error' => 'Student profile not found'], 404);
        }

        // Fetch recent medical reports (last 5)
        $recentReports = MedicalReport::where('student_id', $student->id)
            ->select('id', 'report_date', 'medical_condition', 'created_at')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($report) {
                return [
                    'type' => 'medical_report',
                    'id' => $report->id,
                    'description' => "New medical report: " . Str::limit($report->medical_condition, 50),
                    'date' => $report->created_at->format('Y-m-d H:i:s'),
                ];
            });

        // Fetch recent messages (last 5) from conversations
        $recentMessages = Message::whereHas('conversation', function ($query) use ($user) {
            $query->where('student_id', $user->id);
        })
            ->select('id', 'content', 'created_at')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($message) {
                return [
                    'type' => 'message',
                    'id' => $message->id,
                    'description' => "New message: " . Str::limit($message->content, 50),
                    'date' => $message->created_at->format('Y-m-d H:i:s'),
                ];
            });

        // Combine and sort by date, limit to 5 total
        $updates = $recentReports->merge($recentMessages)
            ->sortByDesc('date')
            ->take(5)
            ->values();

        return response()->json([
            'status' => 'success',
            'data' => $updates
        ], 200);
    }

    /**
     * Get clinic visit counts by month for a specified year (default: current year).
     */
    public function getClinicVisits(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole('student')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $student = Student::where('user_id', $user->id)->first();
        if (!$student) {
            return response()->json(['error' => 'Student profile not found'], 404);
        }

        // Validate year parameter (optional, defaults to current year)
        $year = $request->query('year', now()->year);
        $validated = validator(['year' => $year], [
            'year' => 'integer|min:2000|max:' . (now()->year + 1),
        ]);

        if ($validated->fails()) {
            return response()->json([
                'error' => $validated->errors()->first()
            ], 422);
        }

        // Initialize monthly counts
        $monthlyVisits = array_fill(1, 12, 0);

        // Query clinic visits (medical reports) for the specified year
        $visits = MedicalReport::where('student_id', $student->id)
            ->whereYear('report_date', $year)
            ->select(DB::raw('MONTH(report_date) as month'), DB::raw('COUNT(*) as count'))
            ->groupBy('month')
            ->get();

        // Populate monthly counts
        foreach ($visits as $visit) {
            $monthlyVisits[$visit->month] = $visit->count;
        }

        // Prepare response data
        $data = [
            'year' => (int) $year,
            'visits' => array_values($monthlyVisits),
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];

        return response()->json([
            'status' => 'success',
            'data' => $data
        ], 200);
    }
}
