<?php

namespace PowerComponents\LivewirePowerGrid\Tests\Actions;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};

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
    }

    public static function seed(array $dishes = []): void
    {
        Schema::disableForeignKeyConstraints();

        DB::table('categories')->truncate();
        DB::table('dishes')->truncate();

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

        if (empty($dishes)) {
            $dishes = self::generate();
        }

        DB::table('dishes')->insert($dishes);
    }

    public static function generate(): array
    {
        $dishes = collect([
            [
                'name'        => 'Pastel de Nata',
                'category_id' => 6,
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
                'price'       => 20.50,
                'in_stock'    => true,
                'produced_at' => '2021-02-02 00:00:00',
                'chef_name'   => 'Nábia',
                'diet'        => 1,
            ],
            [
                'name'        => 'Carne Louca',
                'category_id' => 1,
                'price'       => 30.00,
                'in_stock'    => true,
                'produced_at' => '2021-03-03 00:00:00',
                'chef_name'   => '',
                'diet'        => 1,
            ],
            [
                'name'        => 'Bife à Rolê',
                'category_id' => 1,
                'price'       => 40.50,
                'in_stock'    => true,
                'produced_at' => '2021-04-04 00:00:00',
                'diet'        => 1,
            ],
            [
                'name'        => 'Francesinha vegana',
                'category_id' => 2,
                'price'       => 50.00,
                'in_stock'    => true,
                'produced_at' => '2021-05-05 00:00:00',
            ],
            [
                'name'        => 'Francesinha',
                'category_id' => 1,
                'price'       => 60.50,
                'in_stock'    => false,
                'produced_at' => '2026-06-06 00:00:00',
            ],
            [
                'name'        => 'Barco-Sushi da Sueli',
                'category_id' => 1,
                'price'       => 70.00,
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
                'price'       => 80.40,
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
                'price'       => 90.10,
                'in_stock'    => false,
                'produced_at' => '2021-09-09 00:00:00',
            ],
            [
                'name'        => 'борщ',
                'category_id' => 7,
                'price'       => 100.90,
                'in_stock'    => false,
                'produced_at' => '2021-10-10 00:00:00',
            ],
            ['name' => 'Bife à Parmegiana', 'category_id' => 1],
            ['name' => 'Berinjela à Parmegiana', 'category_id' => 4],
            ['name' => 'Almôndegas ao Sugo', 'category_id' => 1],
            ['name' => 'Filé Mignon à parmegiana', 'category_id' => 1],
            ['name' => 'Strogonoff de Filé Mignon', 'category_id' => 1],
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

            return $dish;
        })->toArray();
    }
}
