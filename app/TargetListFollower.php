<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TargetListFollower extends Model
{
    protected $visible =[
        'description','id_str','following','target_lists_id'
    ];
    public function target_list(){
        return $this->belongsTo('App\TargetList');
    }
}
