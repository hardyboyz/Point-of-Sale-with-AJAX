<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PenjualanDetail extends Model
{
    protected $table = 'penjualan_detail';
	protected $primaryKey = 'id_penjualan_detail';

    public function penjualan(){
        return $this->belongsTo('App\Penjualan', 'id_penjualan','id_penjualan');
    }

    public function produk(){
        return $this->belongsTo('App\Produk', 'kode_produk','kode_produk');
    }
}


