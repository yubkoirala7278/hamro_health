<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ParentInfo>
 */
class ParentInfoFactory extends Factory
{
    protected $model = \App\Models\ParentInfo::class;

    public function definition(): array
    {
        // Common Nepali cities/towns for addresses
        $cities = [
            'Kathmandu', 'Pokhara', 'Biratnagar', 'Lalitpur', 'Bhaktapur', 'Bharatpur',
            'Dharan', 'Butwal', 'Hetauda', 'Nepalgunj', 'Itahari', 'Dhangadhi'
        ];

        return [
            'user_id' => null, // Will be set in UserFactory
            'full_name' => $this->faker->name,
            'home_address' => $this->faker->randomElement($cities) . ', ' . $this->faker->streetAddress,
            'created_at' => $this->faker->dateTimeThisYear,
            'updated_at' => $this->faker->dateTimeThisYear,
        ];
    }
}
