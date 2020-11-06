<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LikeWord extends Model
{
    protected $fillable = ['id','word','type_word_id','account_id'];

    public function account(){
        return $this->belongsTo('App\Account');
    }
    public function type_words()
    {
        return $this->hasOne('App\TypeWord');
    }
}
