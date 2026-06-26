<?php


namespace Database\Factories;

use App\Modules\Core\Models\User;
use App\Modules\Jobs\Models\ITJob;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Jobs\Models\Job>
 */
class JobFactory extends Factory
{

protected $model = ITJob::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(3);
        return [
            'title' => $title,
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(['active', 'inactive']),
            'organization_id' => null,
            'created_by' => null,
            'updated_by' => null,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'active']);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'inactive']);
    }

    public function forOrganization(int $organizationId): static
    {
        return $this->state(fn (array $attributes) => ['organization_id' => $organizationId]);
    }

    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
    }
}
