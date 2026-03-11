<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ExecutionCrawlerFactory extends Factory {

    public function definition(): array {
        return [
            'guild_name' => $this->faker->word(),
            'url' => $this->faker->url(),
            'qtd_characters' => $this->faker->numberBetween(1, 100),
            'qtd_character_online' => $this->faker->numberBetween(0, 50),
            'qtd_character_offline' => $this->faker->numberBetween(0, 50),
            'execution_time' => $this->faker->numberBetween(1, 10),
            'scraping_time' => $this->faker->randomFloat(2, 0.1, 5.0),
            'request_time' => $this->faker->randomFloat(2, 0.1, 5.0),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function recentExecution(): static {
        return $this->state(['created_at' => now()->subSeconds(30)]);
    }

    public function outdatedExecution(): static {
        return $this->state(['created_at' => now()->subSeconds(90)]);
    }
}
