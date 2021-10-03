<?php

namespace PowerComponents\LivewirePowerGrid\Tests;

use Illuminate\Support\Facades\DB;
use Livewire\LivewireServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Application;
use PowerComponents\LivewirePowerGrid\Providers\PowerGridServiceProvider;
use \Orchestra\Testbench\TestCase as BaseTestCase;

use function Pest\Faker\faker;

class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->migrations();
        $this->seeders();
    }

    protected function migrations(): void
    {
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
            $table->boolean('in_stock')->default(false);
            $table->boolean('active')->default(true);
            $table->date('produced_at');
            $table->timestamps();
        });
    }
    protected function seeders()
    {
        DB::table('categories')->insert([
            ['name' => 'Carnes'],
            ['name' => 'Peixe'],
            ['name' => 'Tortas'],
            ['name' => 'Acompanhamentos'],
            ['name' => 'Massas'],
            ['name' => 'Sobremesas'],
            ['name' => 'Sopas'],
        ]);

        $dishes = [
            ['name' => 'Pastel de Nata', 'category_id' => 6],
            ['name' => 'Peixada da chef Nábia', 'category_id' => 1],
            ['name' => 'Carne Louca', 'category_id' => 1],
            ['name' => 'Bife à Rolê', 'category_id' => 1],
            ['name' => 'Salmão a Fiorentina', 'category_id' => 2],
            ['name' => 'Francesinha', 'category_id' => 1],
            ['name' => 'Barco-Sushi da Sueli', 'category_id' => 1],
            ['name' => 'Barco-Sushi Simples', 'category_id' => 1],
            ['name' => 'Polpetone Filé Mignon', 'category_id' => 1],
            ['name' => 'борщ', 'category_id' => 7],
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
        ];

        $faker = faker();

        foreach ($dishes as $dish) {
            $dish = [
                'name' => $dish['name'],
                'category_id' => $dish['category_id'],
                'price' => $faker->randomFloat(2, 50, 200),
                'calories' => $faker->biasedNumberBetween($min = 40, $max = 890, $function = 'sqrt'),
                'in_stock' => $faker->boolean(),
                'produced_at' => $faker->dateTimeBetween($startDate = '-1 months', $endDate = 'now')->format("Y-m-d")
            ];
            DB::table('dishes')->insert($dish);
        }
    }

    /**
     * Define environment setup.
     *
     * @param Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('app.key', 'base64:RygUQvaR926QuH4d5G6ZDf9ToJEEeO2p8qDSCq6emPk=');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function getPackageProviders($app)
    {
        return [
            LivewireServiceProvider::class,
            PowerGridServiceProvider::class,
        ];
    }
}
