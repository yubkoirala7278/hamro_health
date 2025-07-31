<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ChildMedicine>
 */
class ChildMedicineFactory extends Factory
{
    protected $model = \App\Models\ChildMedicine::class;

    public function definition(): array
    {
        $medicines = ['Amoxicillin', 'Paracetamol', 'Ibuprofen', 'Children Vitamin'];
        $dosages = ['250mg', '500mg', '5ml', '1 Gummy'];
        $frequencies = ['3 times daily', 'Every 8 hours', 'Once daily', 'Twice daily'];
        $durations = ['7 days', '5 days', 'Ongoing'];
        $statuses = ['Given', 'Due Now', 'Pending'];

        return [
            'child_id' => null, // Set in ChildrenInfoFactory
            'medicine_name' => $this->faker->randomElement($medicines),
            'dosage' => $this->faker->randomElement($dosages),
            'frequency' => $this->faker->randomElement($frequencies),
            'duration' => $this->faker->randomElement($durations),
            'next_dose_due' => $this->faker->dateTimeBetween('now', '+1 week'),
            'status' => $this->faker->randomElement($statuses),
            'created_at' => $this->faker->dateTimeThisYear,
            'updated_at' => $this->faker->dateTimeThisYear,
        ];
    }
}
