<?php

namespace Database\Factories\Back\Catalog;

use App\Models\Back\Catalog\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{

    protected $model = Category::class;


    public function definition(): array
    {
        $name = Str::title($this->faker->unique()->words(2, true));

        return ['name' => $name];
    }


    public function withMedia(): static
    {
        return $this->afterCreating(function (Category $cat) {
            $initial = mb_strtoupper(mb_substr($cat->name, 0, 1));
            $svg     = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="512" height="512">
  <rect width="100%" height="100%" fill="#e9ecef"/>
  <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle"
        font-size="240" font-family="Arial, Helvetica, sans-serif" fill="#6c757d">{$initial}</text>
</svg>
SVG;
            $dir     = storage_path('app/tmp');
            if ( ! is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            $path = $dir . '/cat_' . $cat->id . '.svg';
            file_put_contents($path, $svg);

            try {
                $cat->addMedia($path)->preservingOriginal()->toMediaCollection('icon');
            } finally {
                @unlink($path);
            }
        });
    }
}
