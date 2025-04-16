<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'university_id',
        'description',
        'created_at',
        'updated_at'
    ];
    public function university()
    {
        return $this->belongsTo(University::class);
    }
}
