<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'topic_name',
        'leader_email',
        'description',
        'status',
        'award',
        'submission_year',
        'report_file',
        'topic_image',
        'guidance_teacher',
        ''
    ];

    protected $table = 'topics';

    public $timestamps = false;

    public function members()
    {
        return $this->hasMany(TopicMember::class);
    }

}
