<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posts extends Model
{
    use HasFactory;
    public function post_attachments()
    {
        return $this->hasMany(PostAttachments::class, 'post_id');
    }
    protected $fillable = [ 
        'caption',
        'user_id', 
];
    public $timestamps = false;

}
