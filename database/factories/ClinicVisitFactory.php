<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClinicVisit>
 */
class ClinicVisitFactory extends Factory
{
    protected $model = \App\Models\ClinicVisit::class;

    public function definition(): array
    {
        $reasons = [
            'Routine Checkup', 'Fever', 'Cold', 'Cough', 'Vaccination',
            'Allergy Consultation', 'Injury', 'Stomach Pain', 'Follow-up'
        ];

        return [
            'child_id' => null, // Set in ChildrenInfoFactory
            'visit_date' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'reason' => $this->faker->randomElement($reasons),
            'notes' => $this->faker->optional(0.6)->sentence,
            'created_at' => $this->faker->dateTimeThisYear,
            'updated_at' => $this->faker->dateTimeThisYear,
        ];
    }
}
