<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Mockery\Generator\StringManipulation\Pass\Pass;
use Tests\TestCase;

class StudentApiCrudTest extends TestCase
{
    use DatabaseTransactions;

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
    public function test_login()
    {
        Passport::actingAs(User::find(1));

        $response = $this->get('api/user');

        $response->assertStatus(200);
    }

    public function test_get_students()
    {
        Passport::actingAs(User::find(1));
        $student = Student::factory()->create(['name'=> $this->faker->name]);
        $response = $this->get('api/students');
        $response->assertSee($student->name);
    }

    public function test_delete_student()
    {
        Passport::actingAs(User::find(1));
        $student = Student::factory()->create(['name'=> $this->faker->name]);
        $response = $this->delete('api/students/'.$student->id);
        $response->assertSee("deleted");
        $response->assertStatus(200);
    }

    public function test_update_students()
    {
        Passport::actingAs(User::find(1));
        $student = Student::factory()->create(['name'=> $this->faker->name]);

        $response = $this->put('api/students/'.$student->id,["school_id" => $student->id, "name" => $this->faker->name]);

        $response->assertSee("updated");
        $response->assertStatus(200);
    }

    public function test_get_student()
    {
        Passport::actingAs(User::find(1));
        $student = Student::factory()->create(['name' => $this->faker->name]);

        $response = $this->get('api/students/' . $student->id, ["school_id" => $student->id, "name" => $this->faker->name]);

        $response->assertSee($student->name);
        $response->assertStatus(200);
    }

    public function test_create_student()
    {
        Passport::actingAs(User::find(1));
        $response = $this->post('api/students', ["school_id" => School::all()->random()->id, "name" => $this->faker->name]);
        $response->assertSee('created');
        $response->assertStatus(200);
    }

    public function test_create_validation_school_id_student()
    {
        Passport::actingAs(User::find(1));
        $response = $this->post('api/students', ["school_id" => 0, "name" => $this->faker->name]);
        $response->assertSee("The selected school id is invalid.");
        $response->assertStatus(200);
    }

    public function test_create_validation_name_student()
    {
        Passport::actingAs(User::find(1));
        $school = School::factory()->create();
        $response = $this->post('api/students', ["school_id" => $school->id , "name" => ""]);
        $response->assertSee("The name field is required.");
        $response->assertStatus(200);
    }
}
