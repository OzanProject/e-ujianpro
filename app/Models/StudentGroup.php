<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentGroup extends Model
{
    protected $fillable = ['name', 'created_by'];

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public static function sortCollection($groups)
    {
        return $groups->sortBy(function ($group) {
            $parts = explode(' ', $group->name);
            $first = strtoupper($parts[0]);
            
            $romans = [
                'TK' => 0, 'PAUD' => 0,
                'I' => 1, 'II' => 2, 'III' => 3, 'IV' => 4, 'V' => 5, 'VI' => 6,
                'VII' => 7, 'VIII' => 8, 'IX' => 9, 'X' => 10, 'XI' => 11, 'XII' => 12, 'XIII' => 13
            ];

            $level = 999;
            if (isset($romans[$first])) {
                $level = $romans[$first];
            } elseif (is_numeric($first)) {
                $level = intval($first);
            }

            return sprintf('%03d-%s', $level, $group->name);
        });
    }
}
