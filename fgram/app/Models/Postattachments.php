<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostAttachments extends Model
{
    use HasFactory;
    public function post()
    {
        return $this->belongsTo(Posts::class, 'post_id');
    }
    protected $fillable = [ 
        'storage_path',
        'post_id',
    ];

    public $timestamps = false;

}
