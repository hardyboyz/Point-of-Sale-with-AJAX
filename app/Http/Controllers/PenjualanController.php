<?php

namespace App\Http\Controllers;

use Redirect;
use App\Produk;
use App\Member;
use App\Penjualan;
use Illuminate\Http\Request;
use App\PenjualanDetail;
use Auth;
use DB;

class PenjualanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('penjualan.index'); 
    }

    public function listData($datefrom = '', $dateto = '',$produk = '')
    {

        $penjualan = Penjualan::leftJoin('users', 'users.id', '=', 'penjualan.id_user');
        

        if($datefrom == '' || $dateto ==''){
            $datefrom = date('Y-m-d');
            $dateto = date('Y-m-d');
        }

        if($produk != '-') {
            $penjualan->leftJoin('penjualan_detail as A','A.id_penjualan','=','penjualan.id_penjualan');
            $penjualan->leftJoin('produk as C','A.kode_produk','=','C.kode_produk');
            $penjualan->where('C.nama_produk','like','%'.$produk.'%');
        }

        $penjualan->whereBetween(DB::raw('DATE(transaction_date)'), [$datefrom, $dateto]);
        $penjualan->where('total_harga','>',0);

        $datapenjualan = $penjualan->orderBy('penjualan.id_penjualan', 'desc')->get();
        // dd($datapenjualan);
        
        $no = 0;
        $data = array();
        $total = 0;
        $detail = [];

        foreach($datapenjualan as $list){
            $no ++;
            $row = array();

            $detail = PenjualanDetail::LeftJoin('produk','penjualan_detail.kode_produk','=','produk.kode_produk')
            ->where('id_penjualan',$list->id_penjualan)
            ->select('produk.*','penjualan_detail.*','penjualan_detail.harga_jual as harga')
            ->get();

            $row[] = $no;
            $row[] = date('d M Y H:i:s',strtotime($list->transaction_date));
            $row[] = $list->kode_member;
            $table = '<table class="table tabel-hovered" style="margin-bottom:0px"><tbody>';
            $diskon = 0;
            $subtotal = 0;
            foreach($detail as $d){
                $total = $d->harga * $d->jumlah;
                $subtotal += $total;
                $diskon+=$d->diskon;
                $table.='<tr><th style="font-size:x-small"><span class="badge label-success">'.$d->jumlah.'</span> '.$d->nama_produk.'</th>';
                $table.='<th class="text-right" style="font-size:small">'.number_format($total).'</th></tr>';
            }
            $table .= '</tbody></table>';
            $row[] = $table;
            $row[] = "Rp. ".format_uang($subtotal);
            $row[] = $diskon;
            $row[] = number_format($list->bayar);
            $row[] = $list->name;
            $row[] = '<div class="btn-group">
                    <a onclick="showDetail('.$list->id_penjualan.')" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i></a>
                    <a onclick="deleteData('.$list->id_penjualan.')" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>
                    </div>';
            $data[] = $row;
            $total+= $list->bayar;
        }

        $output = array("data" => $data, "total" => $total);
        return response()->json($output);
    }

    public function mytransaction(){
        return view('transaksi.mytransaction');
    }

    public function listData_mytransaction()
    {
        $today = date('Y-m-d');
        $penjualan = Penjualan::leftJoin('users', 'users.id', '=', 'penjualan.id_user')
        ->leftJoin('member','penjualan.kode_member','=','member.kode_member')
        ->select('users.*', 'penjualan.*', 'member.nama','penjualan.created_at as tanggal')
        ->where('penjualan.id_user',Auth::user()->id)
        ->whereDate('transaction_date',$today)
        ->where('total_harga','>',0)
        ->orderBy('penjualan.id_penjualan', 'desc')
        ->get();

        $no = 0;
        $data = array();
        $total = 0;
        $detail = [];
        foreach($penjualan as $list){
            $no ++;
            $detail = PenjualanDetail::LeftJoin('produk','penjualan_detail.kode_produk','=','produk.kode_produk')
                                    ->where('id_penjualan',$list->id_penjualan)
                                    ->select('produk.*','penjualan_detail.*','penjualan_detail.harga_jual as harga')
                                    ->get();
            $row = array();
            $row[] = $no;
            $row[] = "<span class='label label-success'>".date('d-M-Y H:i', strtotime($list->transaction_date))."</span>";
            $row[] = $list->nama;
            //$row[] = $list->total_item;
            $table = '<table class="table tabel-hovered" style="margin-bottom:0px"><tbody>';
            $diskon = 0;
            $subtotal = 0;
            foreach($detail as $d){
                $total = $d->harga * $d->jumlah;
                $subtotal += $total;
                $diskon += $d->diskon;
                $table.='<tr><td style="font-size:small"><span class="badge label-danger">'.$d->jumlah.'</span> '.$d->nama_produk.'</td>';
                $table.='<td style="font-size:small;text-align:right">'.number_format($d->harga * $d->jumlah).'</td></tr>';
            }
            $table .= '</tbody></table>';
            $row[] = $table;
            $row[] = "Rp. ".format_uang($subtotal);
            $row[] = $diskon;
            // $row[] = "<span class='label label-warning'>Rp. ".format_uang($list->bayar)."</span>";
            $row[] = number_format($list->bayar);
            $row[] = $list->name;
            $row[] = '<div class="btn-group">
                    <a title="Print" href="notapdf/'.$list->id_penjualan.'" class="btn btn-success btn-sm"><i class="fa fa-print"></i></a>
                    <!--<a href="'.$list->id_penjualan.'" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i></a>-->
                    <a onclick="deleteData('.$list->id_penjualan.')" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>
                    </div>';
            $data[] = $row;
            $total+= $list->bayar;
        }

        $output = array("data" => $data, "total" => $total);
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Penjualan  $penjualan
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $detail = PenjualanDetail::leftJoin('produk', 'produk.kode_produk', '=', 'penjualan_detail.kode_produk')
        ->where('id_penjualan', '=', $id)
        ->get();
        $no = 0;
        $data = array();
        $total = 0;
        foreach($detail as $list){
            $no ++;
            $row = array();
            $row[] = $no;
            $row[] = $list->kode_produk;
            $row[] = $list->nama_produk;
            $row[] = "Rp. ".format_uang($list->harga_jual);
            $row[] = $list->jumlah;
            $row[] = number_format($list->sub_total);
            $data[] = $row;
            $total+= $list->sub_total;
        }
    
        $output = array("data" => $data,'total'=>$total);
        return response()->json($output);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Penjualan  $penjualan
     * @return \Illuminate\Http\Response
     */
    public function edit(Penjualan $penjualan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Penjualan  $penjualan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Penjualan $penjualan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Penjualan  $penjualan
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $penjualan = Penjualan::findOrFail($id);
        $penjualan->delete();

        PenjualanDetail::where('id_penjualan', '=', $id)->delete();
        // foreach($detail as $data){
        //     $produk = Produk::where('kode_produk', '=', $data->kode_produk)->first();
        //    // $produk->stok += $data->jumlah;
        //     $produk->update();
        //     $data->delete();
        // }
    }
}
