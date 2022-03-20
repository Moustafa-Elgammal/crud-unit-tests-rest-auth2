<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'students';
    protected $fillable = ['name','school_id','order'];

    /**
     * get Student School
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function school(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    protected static function boot() {
        parent::boot();

        static::saving(function ($student) {
            if ($student->school)
                $student->order = $student->school->getOrder($student->school->id);
            else
                $student->order = 0;
        });
    }
}
