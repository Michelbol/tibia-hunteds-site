<?php

namespace Database\Factories;

use App\Character\TypeEnum;
use App\Character\VocationEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

class CharacterFactory extends Factory {

    public function definition(): array {
        return [
            'name' => $this->faker->name(),
            'vocation' => $this->faker->randomElement(VocationEnum::cases()),
            'guild_name' => $this->faker->name(),
            'level' => $this->faker->numberBetween(0, 2000),
            'joining_date' => $this->faker->date(),
            'type' => $this->faker->randomElement(TypeEnum::cases()),
            'is_online' => $this->faker->boolean(),
            'is_attacker_character' => $this->faker->boolean(),
            'online_at' => $this->faker->date(),
            'position' => $this->faker->name(),
            'position_time' => $this->faker->date(),
        ];
    }
}
