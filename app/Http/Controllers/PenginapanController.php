<?php

namespace App\Http\Controllers;

use App\Penginapan;
use App\Member;
use Illuminate\Http\Request;
use App\Produk;
use Auth;

class PenginapanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $penginapan = Produk::whereIn('id_produk',[497,498])->get();
        $member = Member::orderby('nama','asc')->get();
        $date = date('Y-m-d H:i');
        return view('penginapan.index',compact('penginapan','date','member'));
    }


    public function listData()
    {
        $penginapan = Penginapan::orderBy('id', 'desc')->get();
        
        $no = 0;
        $data = array();
        foreach($penginapan as $list){
            $no ++;
            $jml_hari = $this->getTotalDays($list->tgl_masuk, $list->tgl_keluar);
            $total = ($list->produk->harga_jual * (int) $jml_hari) * $list->jml_kucing;
            $row = array();
            $row[] = "<input type='checkbox' name='id[]'' value='".$list->id."'>";
            $row[] = $no;
            $row[] = $list->member->nama;
            $row[] = $list->nama_kucing;
            $row[] = $list->jml_kucing;
            $row[] = date('Y-m-d H:i', strtotime($list->tgl_masuk));
            $row[] = date('Y-m-d H:i', strtotime($list->tgl_keluar)) ?? '';
            $row[] = $list->kandang == "497" ? "IYA" : "TIDAK";
            $row[] = 'Rp. '.number_format($list->produk->harga_jual);
            $row[] = $list->tgl_keluar == null ? 0 : $jml_hari;
            $row[] = $list->tgl_keluar == null ? 0 : 'Rp. '.number_format($total);
            $row[] = nl2br($list->keterangan);
            $row[] = '<div class="btn-group">
                    <a onclick="editForm('.$list->id.')" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i></a>
                    <a onclick="deleteData('.$list->id.')" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a></div>';
            $data[] = $row;
        }

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
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $penginapan = new Penginapan;
        $penginapan->member_id = $request['member_id'];
        $penginapan->nama_kucing = $request['nama_kucing'];
        $penginapan->jml_kucing = $request['jml_kucing'];
        $penginapan->tgl_masuk = $request['tgl_masuk'];
        $penginapan->tgl_keluar = $request['tgl_keluar'];
        $penginapan->jumlah_hari = $this->getTotalDays($request['tgl_masuk'], $request['tgl_keluar']);
        $penginapan->kandang = $request['kandang'];
        $penginapan->price = $this->get_price($request['kandang']);
        $penginapan->created_by = Auth::user()->id;
        $penginapan->keterangan = $request['keterangan'];
        $penginapan->save();

        echo json_encode(array('msg'=>'success'));

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\penginapan  $penginapan
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $penginapan = Penginapan::find($id);
        return $penginapan;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\penginapan  $penginapan
     * @return \Illuminate\Http\Response
     */
    public function edit(penginapan $penginapan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\penginapan  $penginapan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //dd($request->all());
        $penginapan = Penginapan::find($id);
        //dd($penginapan);
        $penginapan->nama_kucing = $request['nama_kucing'];
        $penginapan->member_id = $request['member_id'];
        $penginapan->jml_kucing = $request['jml_kucing'];
        $penginapan->tgl_masuk = $request['tgl_masuk'];
        $penginapan->tgl_keluar = $request['tgl_keluar'];
        
        $penginapan->jumlah_hari = $this->getTotalDays($penginapan->tgl_masuk, $request['tgl_keluar']);
        $penginapan->kandang = $request['kandang'];
        $penginapan->keterangan = $request->keterangan;
        $penginapan->update();
        echo json_encode(array('msg'=>'success'));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\penginapan  $penginapan
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $kucing = Penginapan::find($id);
        $kucing->delete();
    }

    private function get_price($produk_id){
        $product = Produk::where('id_produk',$produk_id)->first();
        return $product->harga_jual;
    }

    private function getTotalDays($date1, $date2){
        $tgl_masuk = strtotime($date1);
        $tgl_keluar = strtotime($date2);
        $datediff = $tgl_keluar - $tgl_masuk;

        return round($datediff / (60 * 60 * 24));
    }

    // public function insertTransaction(Request $request){
    //     $produk = Produk::create();
    //     $produk->kode_produk = $request['kode_member_date'];
    //     $produk->kategori = 4;
    //     $produk->nama_produk = 4;
    //     $produk->harga_beli = 30000;
    //     $produk->harga_jual = 70000;
    // }
}
