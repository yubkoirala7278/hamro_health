<?php

namespace App\Http\Controllers\web\admin;

use App\Http\Controllers\Controller;
use App\Models\MedicalReport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MedicalReportController extends Controller
{
    // Display a listing of medical reports for a student.
    public function medicalReportsIndex($slug)
    {
        $student = User::with('student')->where('slug', $slug)->firstOrFail();
        if (!Auth::user()->hasRole('admin') && $student->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to student.');
        }
        if (!$student->student) {
            return redirect()->route('admin.students.index')->with('warning', 'Student profile not found.');
        }
        return view('admin.students.medical_reports.index', compact('student'));
    }

    // Provide data for the medical reports DataTables AJAX request.
    public function medicalReportsDataTable(Request $request, $slug)
    {
        if (!$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $student = User::with('student')->where('slug', $slug)->firstOrFail();

            if (!Auth::user()->hasRole('admin') && $student->user_id !== Auth::id()) {
                return response()->json(['error' => 'Unauthorized access to student'], 403);
            }

            if (!$student->student) {
                return response()->json([
                    'draw' => $request->input('draw', 1),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                ]);
            }

            $medicalReports = MedicalReport::with('creator:id,name')
                ->where('student_id', $student->student->id)
                ->select([
                    'id',
                    'student_id',
                    'created_by',
                    'report_date',
                    'medical_condition',
                    'allergies',
                    'medications',
                    'vaccinations',
                    'doctor_name',
                    'specialist',
                    'blood_pressure',
                    'pulse_rate',
                    'temperature',
                    'respiratory_rate',
                    'oxygen_saturation',
                    'status',
                    'created_at'
                ]);

            return DataTables::of($medicalReports)
                ->addIndexColumn()
                ->editColumn('report_date', fn($report) => $report->report_date->format('d M Y'))
                ->editColumn('created_at', fn($report) => $report->created_at->format('d M Y'))
                ->addColumn('created_by_name', fn($report) => optional($report->creator)->name ?? 'N/A')

                // Limit long text fields (you can adjust 50 to your preferred character limit)
                ->editColumn('medical_condition', fn($report) => Str::limit(strip_tags($report->medical_condition), 20))
                ->editColumn('allergies', fn($report) => Str::limit(strip_tags($report->allergies), 20))
                ->editColumn('medications', fn($report) => Str::limit(strip_tags($report->medications), 20))
                ->editColumn('vaccinations', fn($report) => Str::limit(strip_tags($report->vaccinations), 20))

                ->addColumn('action', fn($report) => view('admin.students.medical_reports.action', [
                    'report' => $report,
                    'student' => $student
                ])->render())
                ->rawColumns(['action'])
                ->make(true);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server error occurred'], 500);
        }
    }

    // Show the form for creating a new medical report.
    public function medicalReportsCreate($slug)
    {
        $student = User::with('student')->where('slug', $slug)->firstOrFail();
        if (!Auth::user()->hasRole('admin') && $student->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to student.');
        }
        if (!$student->student) {
            return redirect()->route('admin.students.index')->with('warning', 'Student profile not found.');
        }
        return view('admin.students.medical_reports.create', compact('student'));
    }

    // Store a newly created medical report in storage.
    public function medicalReportsStore(Request $request, $slug)
    {
        $student = User::with('student')->where('slug', $slug)->firstOrFail();
        if (!Auth::user()->hasRole('admin') && $student->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to student.');
        }
        if (!$student->student) {
            return redirect()->route('admin.students.index')->with('warning', 'Student profile not found.');
        }

        $validated = $request->validate([
            'report_date' => ['required', 'date', 'before_or_equal:today'],
            'medical_condition' => ['nullable', 'string', 'max:255'],
            'allergies' => ['nullable', 'string'],
            'medications' => ['nullable', 'string'],
            'vaccinations' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'doctor_name' => ['nullable', 'string', 'max:255'],
            'doctor_contact' => ['nullable', 'string', 'max:20'],
            'specialist' => ['nullable', 'string', 'max:100'],
            'mnc_number' => ['nullable', 'string', 'max:100'],
            'blood_pressure' => ['nullable', 'string', 'max:20'],
            'pulse_rate' => ['nullable', 'string', 'max:20'],
            'temperature' => ['nullable', 'string', 'max:10'],
            'respiratory_rate' => ['nullable', 'string', 'max:10'],
            'oxygen_saturation' => ['nullable', 'string', 'max:10'],
            'report_file' => ['nullable', 'file', 'mimes:pdf,jpg,png', 'max:2048'], // 2MB max
            'status' => ['required', 'in:active,archived'],
        ]);

        try {
            DB::beginTransaction();

            $reportData = [
                'student_id' => $student->student->id,
                'created_by' => Auth::id(),
                'report_date' => $validated['report_date'],
                'medical_condition' => $validated['medical_condition'],
                'allergies' => $validated['allergies'],
                'medications' => $validated['medications'],
                'vaccinations' => $validated['vaccinations'],
                'notes' => $validated['notes'],
                'doctor_name' => $validated['doctor_name'],
                'doctor_contact' => $validated['doctor_contact'],
                'specialist' => $validated['specialist'],
                'mnc_number' => $validated['mnc_number'],
                'blood_pressure' => $validated['blood_pressure'],
                'pulse_rate' => $validated['pulse_rate'],
                'temperature' => $validated['temperature'],
                'respiratory_rate' => $validated['respiratory_rate'],
                'oxygen_saturation' => $validated['oxygen_saturation'],
                'status' => $validated['status'],
            ];

            if ($request->hasFile('report_file')) {
                $filePath = $request->file('report_file')->store('medical_reports', 'public');
                $reportData['report_file'] = $filePath;
            }

            MedicalReport::create($reportData);

            DB::commit();

            return redirect()->route('admin.students.medical_reports.index', $student->slug)
                ->with('success', 'Medical report created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create medical report: ' . $e->getMessage());
        }
    }

    // Display the specified medical report.
    public function medicalReportsShow($slug, $id)
    {
        $student = User::with('student')->where('slug', $slug)->firstOrFail();
        if (!Auth::user()->hasRole('admin') && $student->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to student.');
        }
        if (!$student->student) {
            return redirect()->route('admin.students.index')->with('warning', 'Student profile not found.');
        }
        $report = MedicalReport::where('student_id', $student->student->id)->findOrFail($id);
        return view('admin.students.medical_reports.show', compact('student', 'report'));
    }

    // Show the form for editing the specified medical report.
    public function medicalReportsEdit($slug, $id)
    {
        $student = User::with('student')->where('slug', $slug)->firstOrFail();
        if (!Auth::user()->hasRole('admin') && $student->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to student.');
        }
        if (!$student->student) {
            return redirect()->route('admin.students.index')->with('warning', 'Student profile not found.');
        }
        $report = MedicalReport::where('student_id', $student->student->id)->findOrFail($id);
        return view('admin.students.medical_reports.edit', compact('student', 'report'));
    }
    // Update the specified medical report in storage.
    public function medicalReportsUpdate(Request $request, $slug, $id)
    {
        $student = User::with('student')->where('slug', $slug)->firstOrFail();
        if (!Auth::user()->hasRole('admin') && $student->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to student.');
        }
        if (!$student->student) {
            return redirect()->route('admin.students.index')->with('warning', 'Student profile not found.');
        }
        $report = MedicalReport::where('student_id', $student->student->id)->findOrFail($id);

        $validated = $request->validate([
            'report_date' => ['required', 'date', 'before_or_equal:today'],
            'medical_condition' => ['nullable', 'string', 'max:255'],
            'allergies' => ['nullable', 'string'],
            'medications' => ['nullable', 'string'],
            'vaccinations' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'doctor_name' => ['nullable', 'string', 'max:255'],
            'doctor_contact' => ['nullable', 'string', 'max:20'],
            'specialist' => ['nullable', 'string', 'max:100'],
            'mnc_number' => ['nullable', 'string', 'max:100'],
            'blood_pressure' => ['nullable', 'string', 'max:20'],
            'pulse_rate' => ['nullable', 'string', 'max:20'],
            'temperature' => ['nullable', 'string', 'max:10'],
            'respiratory_rate' => ['nullable', 'string', 'max:10'],
            'oxygen_saturation' => ['nullable', 'string', 'max:10'],
            'report_file' => ['nullable', 'file', 'mimes:pdf,jpg,png', 'max:2048'],
            'status' => ['required', 'in:active,archived'],
        ]);

        try {
            DB::beginTransaction();

            $reportData = [
                'report_date' => $validated['report_date'],
                'medical_condition' => $validated['medical_condition'],
                'allergies' => $validated['allergies'],
                'medications' => $validated['medications'],
                'vaccinations' => $validated['vaccinations'],
                'notes' => $validated['notes'],
                'doctor_name' => $validated['doctor_name'],
                'doctor_contact' => $validated['doctor_contact'],
                'specialist' => $validated['specialist'],
                'mnc_number' => $validated['mnc_number'],
                'blood_pressure' => $validated['blood_pressure'],
                'pulse_rate' => $validated['pulse_rate'],
                'temperature' => $validated['temperature'],
                'respiratory_rate' => $validated['respiratory_rate'],
                'oxygen_saturation' => $validated['oxygen_saturation'],
                'status' => $validated['status'],
            ];

            if ($request->hasFile('report_file')) {
                // Delete old file if exists
                if ($report->report_file) {
                    Storage::disk('public')->delete($report->report_file);
                }
                $filePath = $request->file('report_file')->store('medical_reports', 'public');
                $reportData['report_file'] = $filePath;
            }

            $report->update($reportData);

            DB::commit();

            return redirect()->route('admin.students.medical_reports.index', $student->slug)
                ->with('success', 'Medical report updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update medical report: ' . $e->getMessage());
        }
    }


    // Update the specified medical report in storage.
    public function medicalReportsDestroy($slug, $id)
    {
        try {
            $student = User::with('student')->where('slug', $slug)->firstOrFail();
            if (!Auth::user()->hasRole('admin') && $student->user_id !== Auth::id()) {
                return response()->json(['error' => 'Unauthorized access to student'], 403);
            }
            if (!$student->student) {
                return response()->json(['error' => 'Student profile not found'], 404);
            }
            $report = MedicalReport::where('student_id', $student->student->id)->findOrFail($id);

            // Delete associated file
            if ($report->report_file) {
                Storage::disk('public')->delete($report->report_file);
            }

            $report->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Medical report deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete medical report: ' . $e->getMessage()
            ], 500);
        }
    }
}
