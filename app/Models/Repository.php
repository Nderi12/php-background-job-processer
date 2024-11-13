<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repository extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'github_id', 'owner', 'description', 'html_url', 'branches_url'
    ];

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }
}
