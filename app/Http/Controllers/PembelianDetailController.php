<?php

namespace App\Http\Controllers;

use Redirect;
use App\Pembelian;
use App\Supplier;
use App\Produk;
use App\PembelianDetail;
use Illuminate\Http\Request;

class PembelianDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $produk = Produk::all();
        $idpembelian = session('idpembelian');
        $supplier = Supplier::find(session('idsupplier'));
        return view('pembelian_detail.index', compact('produk', 'idpembelian', 'supplier'));
    }

    public function listData($id)
    {

        $detail = PembelianDetail::leftJoin('produk', 'produk.kode_produk', '=', 'pembelian_detail.kode_produk')
            ->where('id_pembelian', '=', $id)
            ->orderBy('pembelian_detail.id_pembelian_detail','desc')
            ->get(['*','pembelian_detail.harga_beli as harga_beli_detail']);
            //dd($detail);
        $no = 0;
        $data = array();
        $total = 0;
        $total_item = 0;
        foreach($detail as $list){
            $no ++;
            $row = array();
            $row[] = $no;
            $row[] = $list->kode_produk;
            $row[] = $list->nama_produk;
            $row[] = "Rp.<input type='number' class='form-control' name='harga_beli_$list->id_pembelian_detail' value='$list->harga_beli_detail' onChange='changeCount($list->id_pembelian_detail)' style='width:9em'>";

            $row[] = "<input type='number' class='form-control' name='jumlah_$list->id_pembelian_detail' value='$list->jumlah' onChange='changeCount($list->id_pembelian_detail)' style='width:6em'>";
            $row[] = "Rp. ".format_uang($list->harga_beli_detail * $list->jumlah);
            $row[] = '<a onclick="deleteItem('.$list->id_pembelian_detail.')" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>';
            $data[] = $row;

            $total += $list->harga_beli_detail * $list->jumlah;
            $total_item += $list->jumlah;
        }

        $data[] = array("<span class='hide total'>$total</span><span class='hide totalitem'>$total_item</span>", "", "", "", "", "", "");
        
        $output = array("data" => $data);
        return response()->json($output);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $produk = Produk::where('kode_produk', '=', $request['kode'])->first();
        $detail = new PembelianDetail;
        $detail->id_pembelian = $request['idpembelian'];
        $detail->kode_produk = $request['kode'];
        $detail->harga_beli = $produk->harga_beli;
        $detail->jumlah = 1;
        $detail->sub_total = $produk->harga_beli;
        $detail->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PembelianDetail  $pembelianDetail
     * @return \Illuminate\Http\Response
     */
    public function show(PembelianDetail $pembelianDetail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PembelianDetail  $pembelianDetail
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $produk = Produk::all();
        $idpembelian = $id;
        $pembelian = Pembelian::where('id_pembelian',$idpembelian)->first();
        $supplier = Supplier::where('id_supplier',$pembelian->id_supplier)->first();

        return view('pembelian_detail.index', compact('produk', 'idpembelian', 'supplier','pembelian'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PembelianDetail  $pembelianDetail
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $nama_input = "jumlah_".$id;
        $harga_beli = "harga_beli_".$id;
        $detail = PembelianDetail::find($id);
        $detail->jumlah = $request[$nama_input];
        $detail->harga_beli = $request[$harga_beli];
        $detail->sub_total = $detail->harga_beli * $request[$nama_input];

        $produk = Produk::where('kode_produk',$detail->kode_produk)->first();
        $produk->harga_beli = $request[$harga_beli];
        $produk->save();

        $detail->update();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PembelianDetail  $pembelianDetail
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $detail = PembelianDetail::find($id);
        $detail->delete();
    }

    public function loadForm($diskon = 0, $total = 0){
        $bayar = $total - $diskon;
        $data = array(
            "totalrp" => format_uang($total),
            "bayar" => $bayar,
            "bayarrp" => format_uang($bayar),
            "terbilang" => ucwords(terbilang($bayar))." Rupiah"
        );
        return response()->json($data);
    }
}
