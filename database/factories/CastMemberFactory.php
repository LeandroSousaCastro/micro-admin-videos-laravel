<?php

namespace Database\Factories;

use Core\CastMember\Domain\Enum\CastMemberType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CastMember>
 */
class CastMemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $types = $this->faker->randomElement([
            CastMemberType::ACTOR,
            CastMemberType::DIRECTOR
        ]);
        return [
            'id' => (string) Str::uuid(),
            'name' => $this->faker->name(),
            'type' => $types,
        ];
    }
}
