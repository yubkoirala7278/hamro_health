<?php

namespace Database\Factories;

use App\Models\Gender;
use App\Models\School;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StudentProfile>
 */
class StudentProfileFactory extends Factory
{
    protected $model = StudentProfile::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->student(),
            'school_id' => School::factory(),
            'phone' => $this->faker->phoneNumber(),
            'dob' => $this->faker->dateTimeBetween('-18 years', '-10 years')->format('Y-m-d'),
            'gender' => $this->faker->randomElement(['male', 'female', 'other']),
            'address' => $this->faker->address(),
            'parent_phone' => $this->faker->phoneNumber(),
            'emergency_contact' => $this->faker->phoneNumber(),
            'grade_level' => 'Grade ' . $this->faker->numberBetween(1, 12),
        ];
    }
}

