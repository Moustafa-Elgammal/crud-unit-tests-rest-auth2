<?php

namespace App\Console\Commands;

use App\Models\Student;
use App\Services\Schools\SchoolsServices;
use Illuminate\Console\Command;

class FixStudentOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'students:order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix the students order by school';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $schools = (new SchoolsServices())->getAllSchools();
        foreach ($schools as $school)
        {
            if ($school->trashed())
                continue;

            $order = 1;
            foreach ($school->students as $student)
            {
                $student->order = $order;
                $student->saveQuietly();
                $order += 1;
            }
        }

        return 0;
    }
}
