<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\Student;
use App\Services\Students\StudentsServices;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class StudentsServiceTest extends TestCase
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
    public function test_create_student()
    {
        $service = new StudentsServices();

        $school = School::all()->random();

        request()->merge(['name' => $this->faker->name, 'school_id' => $school->id]);

        $this->assertTrue($service->createStudent(request()));
    }

    public function test_update_student()
    {
        $service = new StudentsServices();

        School::factory()->create();
        Student::factory()->create();
        $school = School::all()->random();
        $student = Student::all()->random();

        $new_name = $this->faker->name;

        request()->merge(['name' => $new_name, 'school_id' => $school->id, 'student' => $student->id]);

        $this->assertTrue($service->updateStudent(request()));
        $this->assertEquals($new_name, $service->getStudentById(request())->name);
    }

    public function test_delete_student()
    {
        $service = new StudentsServices();

        $student = Student::factory()->create();
        request()->merge(['id' => $student->id]);
        $this->assertTrue($service->deleteStudent(request()));
        $this->assertNotNull(Student::withTrashed()->find($student->id)->deleted_at);
    }
}
