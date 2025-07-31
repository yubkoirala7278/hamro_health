<?php

namespace Database\Factories;

use App\Models\ChildMedicalDocument;
use App\Models\ChildMedicalInfo;
use App\Models\ChildMedicine;
use App\Models\ClinicVisit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ChildrenInfo>
 */
class ChildrenInfoFactory extends Factory
{
    protected $model = \App\Models\ChildrenInfo::class;

    public function definition(): array
    {
        $firstNames = [
            'Aarav', 'Aryan', 'Saanvi', 'Ananya', 'Rohan', 'Sita', 'Gita', 'Samir', 'Nisha', 'Kiran',
            'Aditya', 'Pranav', 'Anika', 'Riya', 'Suman', 'Binod', 'Sarita', 'Pooja', 'Nabin', 'Roshan'
        ];
        
        $lastNames = [
            'Shrestha', 'Thapa', 'Rai', 'Gurung', 'Tamang', 'Magar', 'Limbu', 'Karki', 'Dahal', 'Adhikari',
            'Poudel', 'Bhandari', 'Joshi', 'Khanal', 'Bhattarai', 'Lamichhane', 'Pokhrel', 'Dhungana'
        ];

        $phonePrefixes = ['984', '985', '986', '980', '981', '974', '975'];

        return [
            'user_id' => null,
            'school_id' => null,
            'full_name' => $this->faker->randomElement($firstNames) . ' ' . $this->faker->randomElement($lastNames),
            'dob' => $this->faker->dateTimeBetween('-18 years', '-5 years')->format('Y-m-d'),
            'emergency_contact_number' => $this->faker->randomElement($phonePrefixes) . $this->faker->numerify('#######'),
            'created_at' => $this->faker->dateTimeThisYear,
            'updated_at' => $this->faker->dateTimeThisYear,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function ($child) {
            ChildMedicalInfo::factory()->create(['child_id' => $child->id]);
            ChildMedicine::factory()->count(rand(1, 3))->create(['child_id' => $child->id]);
            ChildMedicalDocument::factory()->count(rand(0, 2))->create(['child_id' => $child->id]);
            ClinicVisit::factory()->count(rand(1, 3))->create(['child_id' => $child->id]);
        });
    }
}
