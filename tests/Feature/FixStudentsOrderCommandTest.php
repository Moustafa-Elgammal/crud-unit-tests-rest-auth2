<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\Student;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FixStudentsOrderCommandTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_command_exit()
    {
        $this->artisan('students:order')->assertExitCode(0);
    }

    public function test_command_changes()
    {
        $school_id = School::factory()->create()->id;

        $first = Student::factory()->create(['school_id' => $school_id]);

        Student::factory()->times(30)->create(['school_id' => $school_id]);

        $last_order = Student::query()->where('school_id','=',$school_id)->get()->last()->order;

        $first->delete();
        $this->artisan('students:order');

        $last_order_after = Student::query()->where('school_id','=',$school_id)->get()->last()->order;

        $this->assertNotEquals($last_order_after, $last_order);
    }
}
