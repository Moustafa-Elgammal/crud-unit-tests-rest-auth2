<?php

namespace Database\Factories;

use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $school = School::all()->random();
        if (!$school)
            return [];

        return [
            'name' => $this->faker->name,
            'school_id' => $school->id,
            'order' => $school->getOrder($school->id)
        ];
    }
}
