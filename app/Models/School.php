<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'schools';
    protected $fillable = ['name'];

    /** get students of a school
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function students(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Student::class);
    }

    public static function getOrder($id)
    {
        $students = Student::query()->where('school_id','=',$id)->get();
        if(empty($students->toArray()))
            return 1;

        return $students->last()->order + 1;
    }
}
