<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SettingFactory  extends Factory {

    public function definition(): array {
        return [
            'name' => $this->faker->name(),
            'value' => $this->faker->name(),
        ];
    }

}
