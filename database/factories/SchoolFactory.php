<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\School>
 */
class SchoolFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
   public function definition(): array
    {
        // List of realistic Nepali school names (in English)
        $schoolNames = [
            'Adarsha Secondary School',
            'Bal Kalyan Vidyalaya',
            'Bhanubhakta Memorial School',
            'Buddha Academy',
            'Chandra Jyoti School',
            'Durga Bhawani Academy',
            'Gyanodaya Secondary School',
            'Himalaya International School',
            'Janak Secondary School',
            'Kantipur English High School',
            'Little Angels School',
            'Mahendra Vidya Ashram',
            'Narayani Public School',
            'Pashupati Education Foundation',
            'Pragati Shiksha Sadan',
            'Saraswati Secondary School',
            'Shanti Niketan School',
            'Siddhartha English Boarding School',
            'Tribhuvan Adarsha School',
            'Udaya Kharka Secondary School',
            'Valley View English School',
            'Vijaya Samudayik Shiksha Sadan',
            'Yeti International School',
            'Annapurna Secondary School',
            'Bagmati Boarding School',
            'Gaurishankar Academy',
            'Janajyoti Secondary School',
            'Koshi Vidya Mandir',
            'Manakamana English School',
            'Namaste Academy'
        ];

        // Nepali phone number format (98x-xxx-xxxx or 97x-xxx-xxxx)
        $phonePrefixes = ['984', '985', '986', '980', '981', '974', '975'];

        // Common Nepali cities/towns for addresses
        $cities = [
            'Kathmandu', 'Pokhara', 'Biratnagar', 'Lalitpur', 'Bhaktapur', 'Bharatpur', 
            'Dharan', 'Butwal', 'Hetauda', 'Nepalgunj', 'Itahari', 'Dhangadhi'
        ];

        return [
            'name' => $this->faker->randomElement($schoolNames),
            'address' => $this->faker->randomElement($cities) . ', ' . $this->faker->streetAddress,
            'phone' => $this->faker->randomElement($phonePrefixes) . $this->faker->numerify('-###-####'),
            'email' => $this->faker->unique()->companyEmail,
            'created_at' => $this->faker->dateTimeThisYear,
            'updated_at' => $this->faker->dateTimeThisYear,
        ];
    }
}
