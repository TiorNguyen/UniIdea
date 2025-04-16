<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class University extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'image',
        'created_at',
        'updated_at'
    ];

    protected $table = 'universities';

    public function faculties()
    {
        return $this->hasMany(Faculty::class);
    }
}
