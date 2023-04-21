<?php

namespace App\Http\Controllers;

use Redirect;
use Auth;
use PDF;
use App\Penjualan;
use App\Produk;
use App\Member;
use App\Setting;
use App\PenjualanDetail;
use App\PembelianDetail;
use Illuminate\Http\Request;

class PenjualanDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id = 0)
    {
        // $product = Produk::paginate(10);
        $product = Produk::all();
        $member = Member::all();
        $setting = Setting::first();
        $idpenjualan = '';
        $data=[];
        foreach($product as $p){
            //$produk[$c]->push('stock',$this->getStock($p->kode_produk));
            $data[] = ['kode_produk' => $p->kode_produk,
                        'nama_produk' => $p->nama_produk,
                        'harga_jual' => (float)$p->harga_jual,
                        //'stock' => $this->getStock($p->kode_produk)
                    ];
        }
        
        $produk = $data;

        $login = 1;

        if(!empty(session('idpenjualan'))){
            $idpenjualan = session('idpenjualan');
            $login = 0;
            //return view('penjualan_detail.index', compact('produk', 'member', 'setting', 'idpenjualan'));
        }
        
        if($id > 0){
            $login = 0;
            $idpenjualan = $id;
            //return view('penjualan_detail.index', compact('produk', 'member', 'setting', 'idpenjualan'));
        }

        return view('penjualan_detail.index', compact('produk', 'member', 'setting', 'idpenjualan'));

        if($login = 0){
            return view('penjualan_detail.index', compact('produk', 'member', 'setting', 'idpenjualan'));
        }else{
            return Redirect::route('home'); 
        }  
        
    }

    public function getStock($kode_produk){
        $stockIn = PembelianDetail::where('kode_produk',$kode_produk)->sum('jumlah');
        $stockOut = PenjualanDetail::where('kode_produk',$kode_produk)->sum('jumlah');
        return $stockIn - $stockOut;
    }

    public function listData($id)
    {
        $detail = PenjualanDetail::leftJoin('produk', 'produk.kode_produk', '=', 'penjualan_detail.kode_produk')
            ->where('id_penjualan', '=', $id)
            ->orderBy('id_penjualan_detail','desc')
            ->get();
        $no = 0;
        $data = array();
        $total = 0;
        $total_item = 0;
        foreach($detail as $list){
            $no ++;
            $row = array();
            $row[] = $no;
            $row[] = $list->kode_produk;
            $row[] = "<span class='label label-warning' style='font-size:1em'>".$list->nama_produk."</span>";
            $row[] = "<span class='label label-success' style='font-size:15px'>Rp. ".format_uang($list->harga_jual)."</span>";
            $row[] = "<input type='number' class='form-control' name='jumlah_$list->id_penjualan_detail' value='$list->jumlah' onChange='changeCount($list->id_penjualan_detail)' style='width:6em'>";
            $row[] = "<input type='number' class='form-control rowdiskon' name='diskon_$list->id_penjualan_detail' value='$list->diskon' onChange='changeCount($list->id_penjualan_detail)' style='width:6em'>";
            $row[] = "<span class='label label-success' style='font-size:15px'>Rp. ".format_uang($list->sub_total)."</span>";
            $row[] = '<div class="btn-group">
                    <a onclick="deleteItem('.$list->id_penjualan_detail.')" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>';
            $data[] = $row;

            $total += $list->sub_total;
            $total_item += $list->jumlah;
        }

        $data[] = array("<span class='hide total'>$total</span><span class='hide totalitem'>$total_item</span>", "", "", "", "", "", "", "");
        
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

        $check = PenjualanDetail::where('kode_produk',$request['kode'])->where('id_penjualan',$request['idpenjualan']);
        //dd($check->count());
        if($check->count()){
            $data = $check->first();
            $data->jumlah += 1;
            $data->sub_total = ($produk->harga_jual * $data->jumlah) - $produk->diskon;
            $data->save();
        }else{

            $detail = new PenjualanDetail;
            $detail->id_penjualan = $request['idpenjualan'];
            $detail->kode_produk = $request['kode'];
            $detail->harga_jual = $produk->harga_jual;
            $detail->harga_beli = $produk->harga_beli;
            $detail->jumlah = 1;
            $detail->diskon = $produk->diskon;
            $detail->sub_total = $produk->harga_jual - $produk->diskon;
            $detail->save();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PenjualanDetail  $penjualanDetail
     * @return \Illuminate\Http\Response
     */
    public function show(PenjualanDetail $penjualanDetail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PenjualanDetail  $penjualanDetail
     * @return \Illuminate\Http\Response
     */
    public function edit(PenjualanDetail $penjualanDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PenjualanDetail  $penjualanDetail
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $nama_input = "jumlah_".$id;
        $diskon = "diskon_".$id;
        $detail = PenjualanDetail::find($id);
        $total_harga = ($request[$nama_input] * $detail->harga_jual) - $request[$diskon];

        $detail->jumlah = $request[$nama_input];
        $detail->sub_total = $total_harga;
        $detail->diskon = $request[$diskon];
        $detail->update();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PenjualanDetail  $penjualanDetail
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $detail = PenjualanDetail::find($id);
        $detail->delete();
    }

    public function newSession()
    {
        $maxId = Penjualan::max('id_penjualan');

        $count = PenjualanDetail::where('id_penjualan',$maxId)->count();

        if($count){
            $penjualan = new Penjualan; 
            $penjualan->kode_member = 0;    
            $penjualan->total_item = 0;    
            $penjualan->total_harga = 0;    
            $penjualan->diskon = 0;    
            $penjualan->bayar = 0;    
            $penjualan->diterima = 0;    
            $penjualan->id_user = Auth::user()->id;    
            $penjualan->save();
            
            session(['idpenjualan' => $penjualan->id_penjualan]);
        }else{
            session(['idpenjualan' => $maxId]);
        }

        return Redirect::route('transaksi.index');    
    }

    public function saveData(Request $request)
    {
        //dd($request);
        $penjualan = Penjualan::find($request['idpenjualan']);
        $penjualan->kode_member     = $request['member'];
        $penjualan->total_item      = $request['totalitem'];
        $penjualan->total_harga     = $request['total'];
        $penjualan->diskon          = $request['diskon'];
        $penjualan->bayar           = $request['bayar'];
        $penjualan->diterima        = (int) filter_var($request['diterima'],FILTER_SANITIZE_NUMBER_INT);
        $penjualan->kembali         = (int) filter_var($request['kembali'],FILTER_SANITIZE_NUMBER_INT);
        $penjualan->transaction_date = date('Y-m-d H:i:s');
        $penjualan->id_user         = Auth::user()->id;
        $penjualan->update();

       
        return Redirect::route('transaksi.pdf',$request['idpenjualan']);
    }
    
    public function loadForm($diskon=0, $total=0, $diterima=0,$idpenjualan = 0){
        $bayar = $total;
        $kembali = ($diterima != 0) ? $diterima - $bayar : 0;

        $qp = Penjualan::where('id_penjualan',$idpenjualan);
        if($qp->count()){
            $penjualan = $qp->first();
            if($penjualan->diterima > 0){
                // $total = $penjualan->bayar;
                // $bayar = $penjualan->bayar;
                // $kembali = $penjualan->kembali;
                $diterima = $penjualan->diterima;
            }
        }

        $data = array(
            "totalrp" => format_uang($total),
            "bayar" => $bayar,
            "bayarrp" => format_uang($bayar),
            "terbilang" => ucwords(terbilang($bayar))." Rupiah",
            "kembalirp" => format_uang($kembali),
            "kembaliterbilang" => ucwords(terbilang($kembali))." Rupiah",
            "diterima" => $diterima
        );
        return response()->json($data);
    }

    public function printNota()
    {
        $detail = PenjualanDetail::leftJoin('produk', 'produk.kode_produk', '=', 'penjualan_detail.kode_produk')
            ->where('id_penjualan', '=', session('idpenjualan'))
            ->get();

        $penjualan = Penjualan::find(session('idpenjualan'));
        $setting = Setting::find(1);
        
        if($setting->tipe_nota == 0){
            $handle = printer_open(); 
            printer_start_doc($handle, "Nota");
            printer_start_page($handle);

            $font = printer_create_font("Consolas", 100, 80, 600, false, false, false, 0);
            printer_select_font($handle, $font);
            
            printer_draw_text($handle, $setting->nama_perusahaan, 400, 100);

            $font = printer_create_font("Consolas", 72, 48, 400, false, false, false, 0);
            printer_select_font($handle, $font);
            printer_draw_text($handle, $setting->alamat, 50, 200);

            printer_draw_text($handle, date('Y-m-d'), 0, 400);
            printer_draw_text($handle, substr("             ".Auth::user()->name, -15), 600, 400);

            printer_draw_text($handle, "No : ".substr("00000000".$penjualan->id_penjualan, -8), 0, 500);

            printer_draw_text($handle, "============================", 0, 600);
            
            $y = 700;
            
            foreach($detail as $list){           
                printer_draw_text($handle, $list->kode_produk." ".$list->nama_produk, 0, $y+=100);
                printer_draw_text($handle, $list->jumlah." x ".format_uang($list->harga_jual), 0, $y+=100);
                printer_draw_text($handle, substr("                ".format_uang($list->harga_jual*$list->jumlah), -10), 850, $y);

                if($list->diskon != 0){
                    printer_draw_text($handle, "Diskon", 0, $y+=100);
                    printer_draw_text($handle, substr("                      -".format_uang($list->diskon/100*$list->sub_total), -10),  850, $y);
                }
            }
            
            printer_draw_text($handle, "----------------------------", 0, $y+=100);

            printer_draw_text($handle, "Total Harga: ", 0, $y+=100);
            printer_draw_text($handle, substr("           ".format_uang($penjualan->total_harga), -10), 850, $y);

            printer_draw_text($handle, "Total Item: ", 0, $y+=100);
            printer_draw_text($handle, substr("           ".$penjualan->total_item, -10), 850, $y);

            printer_draw_text($handle, "Diskon Member: ", 0, $y+=100);
            printer_draw_text($handle, substr("           ".$penjualan->diskon."%", -10), 850, $y);

            printer_draw_text($handle, "Total Bayar: ", 0, $y+=100);
            printer_draw_text($handle, substr("            ".format_uang($penjualan->bayar), -10), 850, $y);

            printer_draw_text($handle, "Diterima: ", 0, $y+=100);
            printer_draw_text($handle, substr("            ".format_uang($penjualan->diterima), -10), 850, $y);

            printer_draw_text($handle, "Kembali: ", 0, $y+=100);
            printer_draw_text($handle, substr("            ".format_uang($penjualan->diterima-$penjualan->bayar), -10), 850, $y);
            

            printer_draw_text($handle, "============================", 0, $y+=100);
            printer_draw_text($handle, "-= TERIMA KASIH =-", 250, $y+=100);
            printer_delete_font($font);
            
            printer_end_page($handle);
            printer_end_doc($handle);
            printer_close($handle);
        }
        
        return view('penjualan_detail.selesai', compact('setting'));
    }

    public function notaPDF($id=0){
        $detail = PenjualanDetail::leftJoin('produk', 'produk.kode_produk', '=', 'penjualan_detail.kode_produk')
            ->where('id_penjualan', '=', session('idpenjualan'))
            ->get();
        $penjualan = Penjualan::find(session('idpenjualan'));

        if($id > 0){
            $detail = PenjualanDetail::leftJoin('produk', 'produk.kode_produk', '=', 'penjualan_detail.kode_produk')
            ->where('id_penjualan', '=', $id)
            ->get();
            $penjualan = Penjualan::find($id);
        }

        $member = Member::where('kode_member',$penjualan->kode_member)->first();

        session()->forget('idpenjualan');
        $setting = Setting::find(1);
        $no = 0;
        $url = \URL::to('transaksi/baru');
        
        // $pdf = PDF::loadView('penjualan_detail.notapdf', compact('detail', 'penjualan', 'setting', 'no'));
        // $pdf->setPaper(array(0,0,550,440), 'potrait');      
        // return $pdf->stream();
        return view('penjualan_detail.notapdf', compact('detail', 'penjualan', 'setting', 'no','url','member'));
    }

}
