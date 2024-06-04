<?php

namespace App\Http\Controllers;

use App\Pembelian;
use App\Penjualan;
use App\PenjualanDetail;
use App\Pengeluaran;
use Illuminate\Http\Request;
use PDF;
use Auth;
use DB;

class LaporanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $awal = date('Y-m-d', mktime(0,0,0, date('m'), 1, date('Y')));
        $akhir = date('Y-m-d');
        return view('laporan.index', compact('awal', 'akhir'));   
    }

    protected function getData($awal, $akhir){
        $no = 0;
        $data = array();
        $pendapatan = 0;
        $total_pendapatan = 0;
        $total_peng = 0;
        $total_penj = 0;
        while(strtotime($awal) <= strtotime($akhir)){
            $tanggal = $awal;
            $awal = date('Y-m-d', strtotime("+1 day", strtotime($awal)));

            $total_penjualan = Penjualan::where('transaction_date', 'LIKE', "$tanggal%")->sum('bayar');
            $total_pembelian = Pembelian::where('created_at', 'LIKE', "$tanggal%")->sum('bayar');
            $total_pengeluaran = Pengeluaran::where('created_at', 'LIKE', "$tanggal%")->sum('nominal');
            $detail = Pengeluaran::where('created_at', 'LIKE', "$tanggal%")->get('jenis_pengeluaran');

            $pendapatan = $total_penjualan - $total_pembelian - $total_pengeluaran;
            $total_pendapatan += $pendapatan;
            $total_penj += $total_penjualan;
            $total_peng += $total_pengeluaran;

            $no ++;
            $row = array();
            $row[] = $no;
            $row[] = tanggal_indonesia($tanggal, false);
            $row[] = format_uang($total_penjualan);
            $row[] = format_uang($total_pembelian);
            $row[] = "<span title='".$detail."'>".format_uang($total_pengeluaran)."</span>";
            $row[] = format_uang($pendapatan);
            $data[] = $row;
        }
        $data[] = array("Total", "", format_uang($total_penj), "", format_uang($total_peng), format_uang($total_pendapatan));

        return $data;
    }

    public function listData($awal, $akhir)
    {   
        $data = $this->getData($awal, $akhir);

        $output = array("data" => $data);
        return response()->json($output);
    }

    public function refresh(Request $request)
    {
        $awal = $request['awal'];
        $akhir = $request['akhir'];
        return view('laporan.index', compact('awal', 'akhir')); 
    }

    public function exportPDF($awal, $akhir){
        $tanggal_awal = $awal;
        $tanggal_akhir = $akhir;
        $data = $this->getData($awal, $akhir);

        $pdf = PDF::loadView('laporan.pdf', compact('tanggal_awal', 'tanggal_akhir', 'data'));
        $pdf->setPaper('a4', 'potrait');
        
        return $pdf->stream();
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function profitloss(){
        return view('laporan.profit_loss');
    }

    public function profitloss_data($datefrom = '', $dateto = '',$produk = ''){
       
        $penjualan = PenjualanDetail::LeftJoin('produk as p','penjualan_detail.kode_produk','=','p.kode_produk');
                                    //->Leftjoin('pembelian_detail as pd','pd.kode_produk','=','p.kode_produk');
        

        if($datefrom == '' || $dateto ==''){
            $datefrom = date('Y-m-d');
            $dateto = date('Y-m-d');
        }

        if($produk != '-') {
            $penjualan->where('p.nama_produk','like','%'.$produk.'%');
        }

        $penjualan->whereBetween(DB::raw('DATE(penjualan_detail.created_at)'), [$datefrom, $dateto]);
        //$penjualan->groupby('p.nama_produk','p.harga_jual','penjualan_detail.sub_total','penjualan_detail.jumlah','harga_beli','penjualan_detail.created_at','id_penjualan');

        $datapenjualan = $penjualan->orderBy('id_penjualan', 'desc')->get(['p.nama_produk','p.harga_jual','penjualan_detail.sub_total as detail_subtotal','penjualan_detail.jumlah as jumlah_beli','p.harga_beli as harga_beli_by_date','penjualan_detail.created_at as tgl_beli','id_penjualan','diskon','penjualan_detail.harga_beli as harga_beli_on_jual']);
        //dd($datapenjualan);
        
        $no = 0;
        $data = array();
        $profit = 0;
        foreach($datapenjualan as $list){
            $row = [];

            $no++;
            $profit = (($list->jumlah_beli * $list->harga_jual) - ($list->jumlah_beli * $list->harga_beli_by_date)) - $list->diskon;

            if($list->harga_beli_on_jual > 0){
                $profit = (($list->jumlah_beli * $list->harga_jual) - ($list->jumlah_beli * $list->harga_beli_on_jual)) - $list->diskon;
            }

            $row[] = $no;
            $row[] = date('d M Y H:i',strtotime($list->tgl_beli));
            $row[] = $list->nama_produk;
            $row[] = $list->jumlah_beli;
            $row[] = number_format($list->detail_subtotal);
            $row[] = number_format($list->diskon);
            $row[] = number_format($profit);
            $data[] = $row;
        }

        $output = array("data" => $data);
        return response()->json($output);
    }
}
