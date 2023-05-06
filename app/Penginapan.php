<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Penginapan extends Model
{
    use softDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'penginapan';

    public function member(){
        return $this->belongsTo('App\Member','member_id','id_member');
    }
    public function produk(){
        return $this->belongsTo('App\Produk','kandang','id_produk');
    }
}
