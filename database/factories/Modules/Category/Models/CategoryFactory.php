<?php

namespace Database\Factories\Modules\Category\Models;

use App\Modules\Category\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'status' => 'active',
            'organization_id' => 1,
            'sort_order' => 0,
        ];
    }
}
