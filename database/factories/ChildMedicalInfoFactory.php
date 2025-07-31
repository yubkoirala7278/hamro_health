<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ChildMedicalInfo>
 */
class ChildMedicalInfoFactory extends Factory
{
    protected $model = \App\Models\ChildMedicalInfo::class;

    public function definition(): array
    {
        $bloodGroups = ['O Positive', 'A Positive', 'B Positive', 'AB Positive', 'O Negative', 'A Negative', 'B Negative', 'AB Negative'];
        $allergies = ['Peanuts', 'Shellfish', 'Pollen', 'Dust', 'None'];
        $vitamins = ['Multivitamins', 'Vitamin D', 'Vitamin C', 'None'];
        $illnesses = ['Fever', 'Cold', 'Cough', 'None'];
        $statuses = ['Vaccination up to date', 'Needs booster', 'Pending checkup'];
        $immunizations = ['Covid 19', 'Flu Shot 2024', 'MMR', 'Tdap', 'Hepatitis B'];

        return [
            'child_id' => null, // Set in ChildrenInfoFactory
            'blood_group' => $this->faker->randomElement($bloodGroups),
            'allergies' => implode(', ', $this->faker->randomElements($allergies, rand(1, 3))),
            'current_vitamins' => implode(', ', $this->faker->randomElements($vitamins, rand(1, 2))),
            'recent_illness' => implode(', ', $this->faker->randomElements($illnesses, rand(1, 2))),
            'last_updated_date' => $this->faker->dateTimeThisYear->format('Y-m-d'),
            'current_status' => $this->faker->randomElement($statuses),
            'annual_checkup' => $this->faker->dateTimeBetween('-1 year', '+1 year')->format('Y-m-d'),
            'immunizations' => implode(', ', $this->faker->randomElements($immunizations, rand(1, 4))),
            'created_at' => $this->faker->dateTimeThisYear,
            'updated_at' => $this->faker->dateTimeThisYear,
        ];
    }
}
