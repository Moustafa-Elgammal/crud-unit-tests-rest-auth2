<?php

namespace Tests\Feature;

use App\Models\School;
use App\Services\Schools\SchoolsServices;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SchoolsServiceTest extends TestCase
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
    public function test_create_school()
    {
       $service = new SchoolsServices();

       request()->merge(['name' => $this->faker->name]);

       $this->assertTrue($service->createSchool(request()));
    }

    public function test_update_school()
    {
        $service = new SchoolsServices();

        $school = School::all()->random();
        $new_name = $this->faker->name;
        request()->merge(['name' => $new_name, 'id' => $school->id]);

        $this->assertTrue($service->updateSchool(request()));
        $this->assertEquals($new_name, $service->getSchoolById(request())->name);
    }

    public function test_delete_school()
    {
        $service = new SchoolsServices();

        $school = School::factory()->create();
        request()->merge(['id' => $school->id]);
        $this->assertTrue($service->deleteSchool(request()));
        $this->assertNotNull(School::withTrashed()->find($school->id)->deleted_at);
    }
}
