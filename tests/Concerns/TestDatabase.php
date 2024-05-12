<?php

namespace PowerComponents\LivewirePowerGrid\Tests\Concerns;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\{Category, Chef};

class TestDatabase
{
    /**
    * Migrate and seed Dish and Category
    *
    * @return void
    */
    public static function up(): void
    {
        self::migrate();
        self::seed();
    }

    /**
    * Drop databases
    *
    * @return void
    */
    public static function down(): void
    {
        Schema::dropIfExists('dishes');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('chefs');
        Schema::dropIfExists('restaurants');
        Schema::dropIfExists('category_chef');
        Schema::dropIfExists('orders');
    }

    public static function migrate(): void
    {
        self::down();

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('dishes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained();
            $table->foreignId('chef_id')->nullable();
            $table->string('name');
            $table->double('price');
            $table->integer('calories');
            $table->integer('diet');
            $table->string('serving_at')->default('pool bar');
            $table->boolean('in_stock')->default(false);
            $table->string('stored_at');
            $table->boolean('active')->default(true);
            $table->datetime('produced_at');
            $table->string('chef_name')->nullable();
            $table->json('additional')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('chefs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('category_chef', function (Blueprint $table) {
            $table->foreignId('chef_id');
            $table->foreignId('category_id');
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('link')->nullable();
            $table->double('tax')->nullable();
            $table->decimal('price')->nullable();
            $table->boolean('is_active')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public static function seed(array $dishes = []): void
    {
        Schema::disableForeignKeyConstraints();

        DB::table('categories')->truncate();
        DB::table('dishes')->truncate();
        DB::table('chefs')->truncate();
        DB::table('restaurants')->truncate();
        DB::table('category_chef')->truncate();
        DB::table('orders')->truncate();

        Schema::enableForeignKeyConstraints();

        DB::table('categories')->insert([
            ['name' => 'Carnes'],
            ['name' => 'Peixe'],
            ['name' => 'Tortas'],
            ['name' => 'Acompanhamentos'],
            ['name' => 'Massas'],
            ['name' => 'Sobremesas'],
            ['name' => 'Sopas'],
        ]);

        DB::table('chefs')->insert([
            ['name' => 'Luan', 'restaurant_id' => 1],
            ['name' => 'Dan', 'restaurant_id' => 1],
            ['name' => 'Vitor', 'restaurant_id' => 1],
            ['name' => 'Claudio', 'restaurant_id' => 1],
        ]);

        DB::table('restaurants')->insert([
            ['name' => 'Not McDonalds'],
        ]);

        DB::table('orders')->insert([
            ['name' => 'Order 1', 'price' => 10.00, 'tax' => 127.30, 'is_active' => true],
            ['name' => 'Order 2', 'price' => 20.00, 'tax' => 259.50, 'is_active' => true],
            ['name' => 'Order 3', 'price' => null, 'tax' => null, 'is_active' => false],
        ]);

        if (empty($dishes)) {
            $dishes = self::generate();
        }

        DB::table('dishes')->insert($dishes);

        $chefCategories = Category::all();

        Chef::query()->get()->each(function (Chef $chef) use ($chefCategories) {
            $chef->categories()->attach($chefCategories->shuffle()->take(rand(1, 4)));
        });
    }

    public static function generate(): array
    {
        $dishes = collect([
            [
                'name'        => 'Pastel de Nata',
                'category_id' => 6,
                'chef_id'     => 1,
                'price'       => 10.00,
                'in_stock'    => true,
                'produced_at' => '2021-01-01 00:00:00',
                'chef_name'   => null,
                'diet'        => 2,
                'serving_at'  => 'table',
            ],
            [
                'name'        => 'Peixada da chef Nábia',
                'category_id' => 1,
                'chef_id'     => 1,
                'price'       => 20.50,
                'in_stock'    => true,
                'produced_at' => '2021-02-02 00:00:00',
                'chef_name'   => 'Nábia',
                'diet'        => 1,
            ],
            [
                'name'        => 'Carne Louca',
                'category_id' => 1,
                'chef_id'     => 1,
                'price'       => 30.00,
                'in_stock'    => true,
                'produced_at' => '2021-03-03 00:00:00',
                'chef_name'   => '',
                'diet'        => 1,
            ],
            [
                'name'        => 'Bife à Rolê',
                'category_id' => 1,
                'chef_id'     => 1,
                'price'       => 40.50,
                'in_stock'    => true,
                'produced_at' => '2021-04-04 00:00:00',
                'diet'        => 1,
            ],
            [
                'name'        => 'Francesinha vegana',
                'category_id' => 2,
                'chef_id'     => 1,
                'price'       => 50.00,
                'in_stock'    => true,
                'produced_at' => '2021-05-05 00:00:00',
            ],
            [
                'name'        => 'Francesinha',
                'category_id' => 1,
                'chef_id'     => 1,
                'price'       => 60.50,
                'in_stock'    => false,
                'produced_at' => '2026-06-06 00:00:00',
            ],
            [
                'name'        => 'Barco-Sushi da Sueli',
                'category_id' => 1,
                'chef_id'     => 1,
                'price'       => 5000.00,
                'in_stock'    => false,
                'produced_at' => '2021-07-07 19:59:59',
                'additional'  => json_encode([
                    [
                        'Hot-roll' => 8,
                        'Temaki'   => 2,
                    ],
                ]),
            ],
            [
                'name'        => 'Barco-Sushi Simples',
                'category_id' => 1,
                'chef_id'     => 1,
                'price'       => 1500.40,
                'in_stock'    => false,
                'produced_at' => '2021-08-08 00:00:00',
                'additional'  => json_encode([
                    [
                        'Hot-roll' => 6,
                        'Temaki'   => 1,
                        'Uramaki'  => 1,
                    ],
                ]),
            ],
            [
                'name'        => 'Polpetone Filé Mignon',
                'category_id' => 1,
                'chef_id'     => 1,
                'price'       => 5000.00,
                'in_stock'    => false,
                'produced_at' => '2021-09-09 00:00:00',
            ],
            [
                'name'        => 'борщ',
                'category_id' => 7,
                'chef_id'     => 1,
                'price'       => 5000.00,
                'in_stock'    => false,
                'produced_at' => '2021-10-10 00:00:00',
            ],
            [
                'name'        => 'Bife à Parmegiana',
                'category_id' => 1,
                'chef_id'     => 1,
            ],
            [
                'name'        => 'Berinjela à Parmegiana',
                'category_id' => 4,
                'chef_id'     => 1,
            ],
            [
                'name'        => 'Almôndegas ao Sugo',
                'category_id' => 1,
                'chef_id'     => 1,
            ],
            [
                'name'        => 'Filé Mignon à parmegiana',
                'category_id' => 1,
                'chef_id'     => 1,
            ],
            [
                'name'        => 'Strogonoff de Filé Mignon',
                'category_id' => 1,
                'chef_id'     => 1,
            ],
        ]);

        $faker = fake();

        return $dishes->map(function ($dish) use ($faker) {
            if (!isset($dish['price'])) {
                $dish['price'] = $faker->randomFloat(2, 50, 200);
            };

            if (!isset($dish['stored_at'])) {
                $dish['stored_at'] = rand(1, 3) . $faker->randomElement(['', 'a', 'b']);
            };

            if (!isset($dish['calories'])) {
                $dish['calories'] = $faker->biasedNumberBetween($min = 40, $max = 890, $function = 'sqrt');
            }

            if (!isset($dish['in_stock'])) {
                $dish['in_stock'] = $faker->boolean();
            }

            if (!isset($dish['produced_at'])) {
                $dish['produced_at'] = $faker->dateTimeBetween($startDate = '-1 months', $endDate = 'now')->format('Y-m-d');
            }

            if (!isset($dish['stored_at'])) {
                $dish['price'] = $faker->randomFloat(2, 50, 200);
            };

            if (!array_key_exists('chef_name', $dish)) {
                $dish['chef_name'] = 'Luan';
            }

            if (!array_key_exists('diet', $dish)) {
                $dish['diet'] = $faker->randomElement([0, 1, 2]); //Diet::cases()
            }

            if (!array_key_exists('serving_at', $dish)) {
                $dish['serving_at'] = 'pool bar';
            }

            if (!isset($dish['additional'])) {
                $dish['additional'] = '{}';
            }

            if (!isset($dish['chef_id'])) {
                $dish['chef_id'] = 1;
            }

            return $dish;
        })->toArray();
    }
}
