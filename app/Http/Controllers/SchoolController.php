<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\JsonResponse;

class SchoolController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $schools = School::all();
            
            if ($schools->isEmpty()) {
                return response()->json([
                    'message' => 'No schools found',
                    'data' => []
                ], 200);
            }

            return response()->json([
                'message' => 'Schools retrieved successfully',
                'data' => $schools
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving schools',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}