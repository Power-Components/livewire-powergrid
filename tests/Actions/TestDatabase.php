<?php

namespace PowerComponents\LivewirePowerGrid\Tests\Actions;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};

use function Pest\Faker\faker;

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
            ],
            [
                'name'        => 'Barco-Sushi Simples',
                'category_id' => 1,
                'price'       => 80.40,
                'in_stock'    => false,
                'produced_at' => '2021-08-08 00:00:00',
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
            ['name' => 'Carne Assada ao Molho Ferrugem', 'category_id' => 1],
            ['name' => 'Kibe Assado Recheado 500g', 'category_id' => 1],
            ['name' => 'Carne Assada ao Molho', 'category_id' => 1],
            ['name' => 'Empadão de Palmito', 'category_id' => 3],
            ['name' => 'Empadão de Alcachofra', 'category_id' => 3],
            ['name' => 'Ratatouille', 'category_id' => 4],
            ['name' => 'Legumes Primavera ', 'category_id' => 4],
            ['name' => 'Purê de Banana Terra Tartufo', 'category_id' => 4],
            ['name' => 'Farofa de Banana da Terra Tartufo 60g', 'category_id' => 4],
            ['name' => 'Cenoura com Chia Tartufo', 'category_id' => 4],
            ['name' => 'Camarão ao Thermidor', 'category_id' => 2],
            ['name' => 'Carne de Panela ao Molho Ferrugem', 'category_id' => 1],
            ['name' => 'Escondidinho de Carne Seca', 'category_id' => 1],
            ['name' => 'Lagarto recheado com Calabresa', 'category_id' => 1],
            ['name' => 'Filé Mignon Ao Vinho', 'category_id' => 1],
            ['name' => 'Filé Mignon comGorgonzola', 'category_id' => 1],
            ['name' => 'Maminha Assada', 'category_id' => 1],
            ['name' => 'Lagarto', 'category_id' => 1],
            ['name' => 'Strogonoff', 'category_id' => 1],
            ['name' => 'Filé Mignon Suíno', 'category_id' => 1],
            ['name' => 'Carne Moída com Legumes', 'category_id' => 1],
            ['name' => 'Carne Moída com Lentilha', 'category_id' => 1],
            ['name' => 'Carne de Panela ao Molho Funghi', 'category_id' => 1],
            ['name' => 'Escondidinho de Cenoura e Carne Seca', 'category_id' => 1],
            ['name' => 'Hamburguer Assado', 'category_id' => 1],
            ['name' => 'Carne na Cerveja Preta', 'category_id' => 1],
            ['name' => 'Picadinho de Carne', 'category_id' => 1],
            ['name' => 'Filé Mignon', 'category_id' => 1],
            ['name' => 'Feijoada da Chef', 'category_id' => 1],
            ['name' => 'Bife à Milanesa', 'category_id' => 1],
            ['name' => 'Filé Mignon à Parmegiana', 'category_id' => 1],
            ['name' => 'Feijoada', 'category_id' => 1],
            ['name' => 'Filé de Peixe', 'category_id' => 2],
            ['name' => 'Saint Peter à Fiorentina', 'category_id' => 2],
            ['name' => 'Salmão ao Molho de Mostarda e Mel', 'category_id' => 2],
            ['name' => 'Salmão ao Molho de Cogumelos', 'category_id' => 2],
            ['name' => 'Filé de Pescada à Milanesa', 'category_id' => 2],
            ['name' => 'Bacalhau Gratinado - 600g', 'category_id' => 2],
            ['name' => 'Filé de Peixe à Dorê', 'category_id' => 2],
            ['name' => 'Filé de Pescada à Dorê', 'category_id' => 2],
            ['name' => 'Quiche Brie com Damasco', 'category_id' => 3],
            ['name' => 'Quiche Alho Poró', 'category_id' => 3],
            ['name' => 'Quiche Festa Três Queijos', 'category_id' => 3],
            ['name' => 'Torta Campestre de Frango', 'category_id' => 3],
            ['name' => 'Torta Média de Frango com Requeijão', 'category_id' => 3],
            ['name' => 'Risoto de Filé Mignon', 'category_id' => 4],
            ['name' => 'Escondidinho de Carne Moída', 'category_id' => 4],
            ['name' => 'Berinjela ao Pomodoro e 4 Queijos', 'category_id' => 4],
            ['name' => 'Creme de Milho', 'category_id' => 4],
            ['name' => 'Batata Assada Três Queijos -', 'category_id' => 4],
            ['name' => 'Batata Assada Bacon com Requeijão', 'category_id' => 4],
            ['name' => 'Purê de Batatas', 'category_id' => 4],
            ['name' => 'Purê de Mandioquinha', 'category_id' => 4],
            ['name' => 'Creme de Espinafre', 'category_id' => 4],
            ['name' => 'Rondellini de Mussarela ao Sugo', 'category_id' => 5],
            ['name' => 'Lasanha de Berinjela Assada ', 'category_id' => 5],
            ['name' => 'Lasanha de Abobrinha Assada', 'category_id' => 5],
            ['name' => 'Lasanha ao Creme Funghi', 'category_id' => 5],
            ['name' => 'Lasanha Verde com Frango ', 'category_id' => 5],
            ['name' => 'Tortelli de Mussarela', 'category_id' => 5],
            ['name' => 'Capeteli Frango in Brodo Tartufo', 'category_id' => 5],
            ['name' => 'Talharim aos 2 Queijos', 'category_id' => 5],
            ['name' => 'Lasanha de Batata Doce', 'category_id' => 5],
            ['name' => 'Rondellini de Mussarela ao Sugo', 'category_id' => 5],
            ['name' => 'Lasanha Margherita', 'category_id' => 5],
            ['name' => 'Lasanha de Espinafre e Queijos', 'category_id' => 5],
            ['name' => 'Lasanha à Bolognesa', 'category_id' => 5],
            ['name' => 'Lasanha Marguerita Média', 'category_id' => 5],
            ['name' => 'Talharim ao Ragú de Costelinha', 'category_id' => 5],
            ['name' => 'Panqueca de Carne com Molho', 'category_id' => 5],
            ['name' => 'Talharim a Bolognesa', 'category_id' => 5],
            ['name' => 'Lasanha de Mussarela', 'category_id' => 5],
            ['name' => 'Bolo de Beijinho', 'category_id' => 6],
            ['name' => 'Fios de Ovos 500g', 'category_id' => 6],
            ['name' => 'Brownie Low Carb', 'category_id' => 6],
            ['name' => 'Creme de Morangos', 'category_id' => 6],
            ['name' => 'Doce de mamão', 'category_id' => 6],
            ['name' => 'Torta de Pêra', 'category_id' => 6],
            ['name' => 'Torta de Limão Siciliano', 'category_id' => 6],
            ['name' => 'Fios de Ovos 250g', 'category_id' => 6],
            ['name' => 'Torta de Baunilha com Berries 450g', 'category_id' => 6],
            ['name' => 'Torta de Chocolate Belga', 'category_id' => 6],
            ['name' => 'Bolo de Brigadeiro Belga BB', 'category_id' => 6],
            ['name' => 'Torta de Brigadeiro Crocante', 'category_id' => 6],
            ['name' => 'Strudel de Maçã', 'category_id' => 6],
            ['name' => 'Sopa de Tomates Assados', 'category_id' => 7],
            ['name' => 'Sopa Creme de Ervilha', 'category_id' => 7],
        ]);

        $faker = faker();

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

            return $dish;
        })->toArray();
    }
}
