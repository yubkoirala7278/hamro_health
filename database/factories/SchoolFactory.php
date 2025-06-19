<?php

namespace Database\Factories;

use App\Models\School;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\School>
 */
class SchoolFactory extends Factory
{
    protected $model = School::class;

    public function definition()
    {
        return [
            'address' => $this->faker->address,
            'phone' => $this->faker->phoneNumber,
            'created_by' => User::factory()->create()->assignRole('admin')->id,
        ];
    }
}
