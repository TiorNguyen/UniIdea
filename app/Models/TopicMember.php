<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopicMember extends Model
{
    use HasFactory;
    protected $table = 'topic_members';
    protected $fillable = [
        'topic_id',
        'name',
        'student_id',
        'university_id',
        'faculty_id',
        'phone',
        'created_at',
        'updated_at'
    ];

    public function university()
    {
        return $this->belongsTo(University::class);
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }
}
