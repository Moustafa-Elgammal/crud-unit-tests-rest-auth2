<?php

namespace App\Listeners;

use App\Events\SendEmailEvent;
use App\Mail\StudentsOrdered;
use App\Models\Student;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class StudentsOrderCommandEnd
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\SendEmailEvent  $event
     * @return void
     */
    public function handle(SendEmailEvent $event)
    {
        Student::factory()->times(50)->create();
        try {
            Mail::to($event->user)->send(new StudentsOrdered());
        } catch (\Exception $e)
        {
            echo "Sending Email Exception:\n";
            echo $e->getMessage();
        }
    }
}
