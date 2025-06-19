<?php

namespace Database\Seeders;

use App\Models\MedicalReport;
use App\Models\School;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'school_admin']);
        Role::firstOrCreate(['name' => 'student']);

        // Create system admin
        $admin = User::factory()->create([
            'name' => 'System Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'slug' => Str::slug('System Admin'),
        ]);
        $admin->assignRole('admin');

        // Create 2 schools with 1 school_admin and 2 students each
        foreach (range(1, 2) as $i) {
            // Create school admin
            $schoolAdmin = User::factory()->create([
                'name' => "School Admin {$i}",
                'email' => "school_admin{$i}@example.com",
                'password' => bcrypt('password'),
                'slug' => Str::slug("School Admin {$i}"),
            ]);
            $schoolAdmin->assignRole('school_admin');

            // Create school (without `school_admin_id`)
            $school = School::create([
                'created_by' => $admin->id,
                'address' => "Address for School {$i}",
                'phone' => "123-456-78{$i}",
            ]);

            // Attach school_admin to school via pivot table
            $school->users()->attach($schoolAdmin->id);

            // Create 2 students for this school
            foreach (range(1, 2) as $j) {
                $student = User::factory()->create([
                    'name' => "Student {$i}-{$j}",
                    'email' => "student{$i}_{$j}@example.com",
                    'password' => bcrypt('password'),
                    'slug' => Str::slug("Student {$i} {$j}"),
                ]);
                $student->assignRole('student');

                // Attach student to the school
                $school->users()->attach($student->id);

                // Create student profile
                StudentProfile::factory()->create([
                    'user_id' => $student->id,
                    'school_id' => $school->id,
                    'phone' => "555-000-{$i}{$j}",
                    'dob' => now()->subYears(rand(10, 18)),
                    'gender' => 'male',
                    'address' => "Student Address {$i}-{$j}",
                    'parent_phone' => "555-111-{$i}{$j}",
                    'emergency_contact' => "555-222-{$i}{$j}",
                    'grade_level' => "Grade " . rand(1, 12),
                ]);

                // Create a medical report uploaded by the school admin
                MedicalReport::factory()->create([
                    'student_id' => $student->id,
                    'school_id' => $school->id,
                    'uploaded_by' => $schoolAdmin->id,
                    'title' => "Health Checkup for Student {$i}-{$j}",
                    'checkup_date' => now()->subDays(rand(1, 365)),
                    'report_type' => 'checkup',
                    'status' => 'pending',
                ]);
            }
        }
    }
}
