<?php

namespace Tests\Feature;

use App\Factories\SchoolFactoryDB;
use App\Models\School;
use App\Repositories\Repository;
use Database\Factories\SchoolFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TestRepositoriesMainClassTest extends TestCase
{

    protected \Faker\Generator $faker;
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        $this->faker = \Faker\Factory::create();

        parent::__construct($name, $data, $dataName);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_add_error()
    {
        $repo = new Repository();
        $errorMessage = $this->faker->name();
        $repo->addError($errorMessage);
        $this->assertContains($errorMessage, $repo->getErrors());
    }

    public function test_add_error_with_reset_previous_errors()
    {
        $repo = new Repository();
        $errorMessage = $this->faker->name();
        $repo->addError($errorMessage);
        $errorMessageNew = $this->faker->name();
        $repo->addError($errorMessageNew, true);
        $this->assertNotContains($errorMessage, $repo->getErrors());
    }

    public function test_create_new_repository()
    {
        $repo = new Repository();
        $data = new \stdClass();
        $data->name = $this->faker->name;
        $this->assertTrue( $repo->create(new \App\Factories\SchoolFactoryDB(), $data));
    }

    public function test_delete_repository()
    {
        $repo = new Repository();
        $model = School::factory()->create();
        $this->assertTrue( $repo->delete(new School(), $model->id));

    }

    public function test_delete_repository_no_null()
    {
        $repo = new Repository();
        $model = School::factory()->create();
        $repo->delete(new School(), $model->id);
        $this->assertNotNull(School::withTrashed()->find($model->id)->deleted_at);

    }

     public function test_get_model_repository()
    {
        $repo = new Repository();
        $schoolName = $this->faker->name;
        $model = School::factory()->create(['name'=> $schoolName]);
        $testModel = $repo->getById(new School(), $model->id);
        $this->assertEquals($testModel->name, $model->name);
    }

    public function test_update_repository()
    {
        $repo = new Repository();

        $school = School::factory()->create();

        $data = new \stdClass();
        $data->name = $this->faker->name;
        $data->id = $school->id;
        $repo->update(new SchoolFactoryDB(), $data);
        $updated = $repo->getById(new School(), $data->id);
        $this->assertEquals($updated->name, $data->name);
    }

    public function test_get_all()
    {
        $repo = new Repository();
        $all = $repo->getAll(new School())->count();
        $check = DB::table('schools')->whereNull('deleted_at',)->get()->count();
        $this->assertEquals($all, $check);
    }


}
