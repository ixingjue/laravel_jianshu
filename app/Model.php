<?php

namespace App;

use Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel
{
    //protected $table="posts2"; 自定义表名，默认为posts
    //protected $fillable=['title','content'];
    protected $guarded = [];//不可以注入的字段
}
