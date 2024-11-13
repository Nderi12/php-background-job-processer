<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commit extends Model
{
    use HasFactory;

    protected $table = 'commits';

    protected $fillable = [
        'branch_id',
        'commit_sha',
        'author_name',
        'author_email',
        'commit_date',
        'message',
    ];
}
