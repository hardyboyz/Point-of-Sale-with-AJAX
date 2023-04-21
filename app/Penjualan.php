<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $table = 'penjualan';
	protected $primaryKey = 'id_penjualan';

    public function PenjualanDetail(){
        return $this->hasMany('App\PenjualanDetail', 'id_penjualan','id_penjualan');
    }
}
