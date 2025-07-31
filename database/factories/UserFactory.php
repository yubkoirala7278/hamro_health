<?php

namespace Database\Factories;

use App\Models\ChildrenInfo;
use App\Models\ParentInfo;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        // List of realistic Nepali first and last names
        $firstNames = [
            'Sanjay', 'Aarav', 'Roshan', 'Sita', 'Gita', 'Ram', 'Shyam', 'Hari', 'Krishna', 'Bimal',
            'Anita', 'Sunita', 'Manish', 'Rajan', 'Pooja', 'Nabin', 'Sarita', 'Dipak', 'Ramesh', 'Suman'
        ];
        
        $lastNames = [
            'Shrestha', 'Thapa', 'Rai', 'Gurung', 'Tamang', 'Magar', 'Limbu', 'Karki', 'Dahal', 'Adhikari',
            'Poudel', 'Bhandari', 'Joshi', 'Khanal', 'Bhattarai', 'Lamichhane', 'Pokhrel', 'Dhungana'
        ];

        // Nepali phone number prefixes (98x or 97x)
        $phonePrefixes = ['984', '985', '986', '980', '981', '974', '975'];

        // Generate full name
        $fullName = $this->faker->randomElement($firstNames) . ' ' . $this->faker->randomElement($lastNames);

        // Get a random school ID
        $schoolId = School::inRandomOrder()->first()->id ?? null;

        return [
            'name' => $fullName,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->randomElement($phonePrefixes) . $this->faker->numerify('#######'),
            'school_id' => $schoolId,
            'email_verified_at' => $this->faker->optional(0.7)->dateTimeThisYear,
            'password' => Hash::make('password123'),
            'remember_token' => Str::random(10),
            'created_at' => $this->faker->dateTimeThisYear,
            'updated_at' => $this->faker->dateTimeThisYear,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function ($user) {
            // Create ParentInfo record
            ParentInfo::factory()->create([
                'user_id' => $user->id,
                'full_name' => $user->name,
            ]);

            // Create ChildrenInfo record
            ChildrenInfo::factory()->create([
                'user_id' => $user->id,
                'school_id' => $user->school_id,
            ]);
        });
    }
}
