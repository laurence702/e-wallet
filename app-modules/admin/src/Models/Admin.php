<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'admins';

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
}
