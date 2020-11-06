<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnfollowUser extends Model
{
    protected $fillable = ['id_str','account_id','id'];
    protected $visible =[
        'id_str',
    ];
    public function account(){
        return $this->belongsTo('App\Account');
    }
}
