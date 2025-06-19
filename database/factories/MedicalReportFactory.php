<?php

namespace Database\Factories;

use App\Models\MedicalReport;
use App\Models\ReportStatus;
use App\Models\ReportType;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MedicalReport>
 */
class MedicalReportFactory extends Factory
{
    protected $model = MedicalReport::class;

    public function definition(): array
    {
        return [
            'student_id' => User::factory()->student(),
            'school_id' => School::factory(),
            'uploaded_by' => User::factory()->schoolAdmin(),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'file_path' => null, // or use $this->faker->filePath()
            'checkup_date' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'report_type' => 'checkup',
            'status' => 'pending',
        ];
    }
}

