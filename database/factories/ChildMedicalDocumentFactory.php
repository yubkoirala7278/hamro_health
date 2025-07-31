<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ChildMedicalDocument>
 */
class ChildMedicalDocumentFactory extends Factory
{
    protected $model = \App\Models\ChildMedicalDocument::class;

    public function definition(): array
    {
        $fileTypes = ['image/jpeg', 'application/pdf'];
        $fileNames = ['medical_report.pdf', 'vaccination_card.jpg', 'allergy_test.pdf', 'checkup_summary.jpg'];

        return [
            'child_id' => null, // Set in ChildrenInfoFactory
            'file_path' => 'storage/medical_documents/q1ajdDfD3o2BMeitSIXpHbMsWA4zctOyeyTTgF0O.webp',
            'file_type' => $this->faker->randomElement($fileTypes),
            'file_name' => $this->faker->randomElement($fileNames),
            'created_at' => $this->faker->dateTimeThisYear,
            'updated_at' => $this->faker->dateTimeThisYear,
        ];
    }
}
