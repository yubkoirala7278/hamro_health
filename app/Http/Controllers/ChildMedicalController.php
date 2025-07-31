<?php

namespace App\Http\Controllers;

use App\Models\ChildrenInfo;
use App\Models\ChildMedicalInfo;
use App\Models\ChildMedicine;
use App\Models\ChildMedicalDocument;
use App\Models\ClinicVisit;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChildMedicalController extends Controller
{

    // Get all medical info, medicines, and documents for a child
    public function show($childId): JsonResponse
    {
        try {
            $user = Auth::user();
            $child = ChildrenInfo::where('id', $childId)->where('user_id', $user->id)->first();

            if (!$child) {
                return response()->json(['message' => 'Child not found or not authorized'], 404);
            }

            $medicalInfo = ChildMedicalInfo::where('child_id', $childId)->first();
            $medicines = ChildMedicine::where('child_id', $childId)->get();
            $documents = ChildMedicalDocument::where('child_id', $childId)->orderBy('created_at', 'desc')->get();

            return response()->json([
                'message' => 'Child medical details retrieved successfully',
                'data' => [
                    'child' => $child,
                    'medical_info' => $medicalInfo,
                    'medicines' => $medicines,
                    'documents' => $documents
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error retrieving child medical details', 'error' => $e->getMessage()], 500);
        }
    }

    // Add a new medicine
    public function storeMedicine(Request $request, $childId): JsonResponse
    {
        try {
            $user = Auth::user();
            $child = ChildrenInfo::where('id', $childId)->where('user_id', $user->id)->first();

            if (!$child) {
                return response()->json(['message' => 'Child not found or not authorized'], 404);
            }

            $validated = $request->validate([
                'medicine_name' => 'required|string|max:255',
                'dosage' => 'required|string|max:100',
                'frequency' => 'required|string|max:100',
                'duration' => 'required|string|max:100',
                'next_dose_due' => 'required|date',
            ]);

            $medicine = ChildMedicine::create([
                'child_id' => $childId,
                'medicine_name' => $validated['medicine_name'],
                'dosage' => $validated['dosage'],
                'frequency' => $validated['frequency'],
                'duration' => $validated['duration'],
                'next_dose_due' => $validated['next_dose_due'],
                'status' => 'Pending'
            ]);

            return response()->json(['message' => 'Medicine added successfully', 'data' => $medicine], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error adding medicine', 'error' => $e->getMessage()], 500);
        }
    }

    // Mark a medicine dose as given
    public function markDoseGiven(Request $request, $childId, $medicineId): JsonResponse
    {
        try {
            $user = Auth::user();
            $child = ChildrenInfo::where('id', $childId)->where('user_id', $user->id)->first();

            if (!$child) {
                return response()->json(['message' => 'Child not found or not authorized'], 404);
            }

            $medicine = ChildMedicine::where('id', $medicineId)->where('child_id', $childId)->first();

            if (!$medicine) {
                return response()->json(['message' => 'Medicine not found'], 404);
            }

            $medicine->update([
                'status' => 'Given',
                'next_dose_due' => $request->next_dose_due ?? $medicine->next_dose_due // Update if provided
            ]);

            return response()->json(['message' => 'Dose marked as given', 'data' => $medicine], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error marking dose as given', 'error' => $e->getMessage()], 500);
        }
    }

    // Upload a medical document
    public function uploadDocument(Request $request, $childId): JsonResponse
    {
        try {
            $user = Auth::user();
            $child = ChildrenInfo::where('id', $childId)->where('user_id', $user->id)->first();

            if (!$child) {
                return response()->json(['message' => 'Child not found or not authorized'], 404);
            }

            $validated = $request->validate([
                'document' => 'required|file|mimes:jpeg,png,pdf,webp,jpg|max:2048', // Max 2MB
            ]);

            $file = $request->file('document');
            $fileName = $file->getClientOriginalName();
            $filePath = $file->store('medical_documents', 'public');
            $fileType = $file->getMimeType();

            $document = ChildMedicalDocument::create([
                'child_id' => $childId,
                'file_path' => $filePath,
                'file_type' => $fileType,
                'file_name' => $fileName
            ]);

            return response()->json(['message' => 'Document uploaded successfully', 'data' => $document], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error uploading document', 'error' => $e->getMessage()], 500);
        }
    }

    // get clicnic visits
    public function getClinicVisits(): JsonResponse
    {
        try {
            $user = Auth::user();
            $children = ChildrenInfo::where('user_id', $user->id)->pluck('id');

            if ($children->isEmpty()) {
                return response()->json(['message' => 'No children found for this user', 'data' => []], 200);
            }

            // Group visits by month for line/bar chart
            $visitsByMonth = ClinicVisit::whereIn('child_id', $children)
                ->select(
                    DB::raw("DATE_FORMAT(visit_date, '%Y-%m') as month"),
                    DB::raw('COUNT(*) as visit_count')
                )
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->month => $item->visit_count];
                });

            // Group visits by child for pie chart
            $visitsByChild = ClinicVisit::whereIn('child_id', $children)
                ->join('children_infos', 'clinic_visits.child_id', '=', 'children_infos.id')
                ->select('children_infos.full_name', DB::raw('COUNT(clinic_visits.id) as visit_count'))
                ->groupBy('children_infos.full_name')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->full_name => $item->visit_count];
                });

            // Detailed visit records
            $visitDetails = ClinicVisit::whereIn('child_id', $children)
                ->join('children_infos', 'clinic_visits.child_id', '=', 'children_infos.id')
                ->select('clinic_visits.*', 'children_infos.full_name')
                ->orderBy('visit_date', 'desc')
                ->get();

            return response()->json([
                'message' => 'Clinic visits retrieved successfully',
                'data' => [
                    'by_month' => $visitsByMonth,
                    'by_child' => $visitsByChild,
                    'details' => $visitDetails
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error retrieving clinic visits', 'error' => $e->getMessage()], 500);
        }
    }

    // Get all medicines for the authenticated user's children
    public function getAllMedicines(): JsonResponse
    {
        try {
            $user = Auth::user();
            $children = ChildrenInfo::where('user_id', $user->id)->pluck('id');

            if ($children->isEmpty()) {
                return response()->json(['message' => 'No children found for this user', 'data' => []], 200);
            }

            $medicines = ChildMedicine::whereIn('child_id', $children)
                ->join('children_infos', 'child_medicines.child_id', '=', 'children_infos.id')
                ->select('child_medicines.*', 'children_infos.full_name as child_name')
                ->orderBy('next_dose_due', 'asc')
                ->get();

            return response()->json([
                'message' => 'Medicines retrieved successfully',
                'data' => $medicines
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error retrieving medicines', 'error' => $e->getMessage()], 500);
        }
    }

    // View details of a specific medicine
    public function showMedicine($childId, $medicineId): JsonResponse
    {
        try {
            $user = Auth::user();
            $child = ChildrenInfo::where('id', $childId)->where('user_id', $user->id)->first();

            if (!$child) {
                return response()->json(['message' => 'Child not found or not authorized'], 404);
            }

            $medicine = ChildMedicine::where('id', $medicineId)
                ->where('child_id', $childId)
                ->with(['child' => function ($query) {
                    $query->select('id', 'full_name');
                }])
                ->first();

            if (!$medicine) {
                return response()->json(['message' => 'Medicine not found'], 404);
            }

            return response()->json([
                'message' => 'Medicine details retrieved successfully',
                'data' => $medicine
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error retrieving medicine details', 'error' => $e->getMessage()], 500);
        }
    }

    // Edit a medicine
    public function updateMedicine(Request $request, $childId, $medicineId): JsonResponse
    {
        try {
            $user = Auth::user();
            $child = ChildrenInfo::where('id', $childId)->where('user_id', $user->id)->first();

            if (!$child) {
                return response()->json(['message' => 'Child not found or not authorized'], 404);
            }

            $medicine = ChildMedicine::where('id', $medicineId)->where('child_id', $childId)->first();

            if (!$medicine) {
                return response()->json(['message' => 'Medicine not found'], 404);
            }

            $validated = $request->validate([
                'medicine_name' => 'required|string|max:255',
                'dosage' => 'required|string|max:100',
                'frequency' => 'required|string|max:100',
                'duration' => 'required|string|max:100',
                'next_dose_due' => 'required|date',
                'status' => 'required|string|in:Given,Due Now,Pending'
            ]);

            $medicine->update($validated);

            return response()->json(['message' => 'Medicine updated successfully', 'data' => $medicine], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating medicine', 'error' => $e->getMessage()], 500);
        }
    }

    // Delete a medicine
    public function deleteMedicine($childId, $medicineId): JsonResponse
    {
        try {
            $user = Auth::user();
            $child = ChildrenInfo::where('id', $childId)->where('user_id', $user->id)->first();

            if (!$child) {
                return response()->json(['message' => 'Child not found or not authorized'], 404);
            }

            $medicine = ChildMedicine::where('id', $medicineId)->where('child_id', $childId)->first();

            if (!$medicine) {
                return response()->json(['message' => 'Medicine not found'], 404);
            }

            $medicine->delete();

            return response()->json(['message' => 'Medicine deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting medicine', 'error' => $e->getMessage()], 500);
        }
    }
}
