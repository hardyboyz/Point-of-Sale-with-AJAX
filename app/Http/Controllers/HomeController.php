<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

use App\Setting;
use App\Kategori;
use App\Produk;
use App\Supplier;
use App\Member;
use App\Penjualan;
use App\PenjualanDetail;
use DB;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $setting = Setting::find(1);

        $awal = date('Y-m-01');
        $akhir = date('Y-m-d');
        $today = date('Y-m-d');

        $tanggal = $awal;
        $data_tanggal = array();
        $data_pendapatan = array();
        $sales_today = 0;

        while(strtotime($tanggal) <= strtotime($akhir)){ 
            $data_tanggal[] = (int)substr($tanggal,8,2);
            
            $pendapatan = Penjualan::where('transaction_date', 'LIKE', "$tanggal%")->sum('bayar');
            $data_pendapatan[] = (int) $pendapatan;

            $tanggal = date('Y-m-d', strtotime("+1 day", strtotime($tanggal)));
        }

        $data = PenjualanDetail::whereDate('created_at', '=', "$today")
                            ->select(
                                DB::raw('(SUM(sub_total) - SUM(harga_beli * jumlah)) as profit_today'),
                                DB::raw('SUM(sub_total) as sales_today'),
                                )
                            ->first();
        $data['profit_today'] = number_format($data->profit_today);
        $data['sales_today'] = number_format($data->sales_today);

        $date1 = date('Y-m-01');
        $date2 = date('Y-m-t');
        $monthly = PenjualanDetail::whereDate('created_at', '>=', "$date1")
                                    ->whereDate('created_at', '<=', "$date2")
                                    ->select(
                                        DB::raw('(SUM(sub_total) - SUM(harga_beli * jumlah)) as profit_this_month'),
                                        DB::raw('SUM(sub_total) as sales_this_month'),
                                        )
                                    ->first();
        $data['profit_this_month'] = number_format($monthly->profit_this_month);
        $data['sales_this_month'] = number_format($monthly->sales_this_month);
        // dd($profit);

        $kategori = Kategori::count();
        $products = Produk::where('produk.id_kategori','!=', 8)
                        ->join('kategori','produk.id_kategori','=','kategori.id_kategori')
                        ->where('stok','<',5)
                        ->where('nama_produk','!=','null')
                        ->orderBy('nama_produk')
                        ->get();
        $produk = Produk::count();
        $supplier = Supplier::count();
        $member = Member::count();
        $item = 0;
        //dd($data_pendapatan);

        if(Auth::user()->level == 1) return view('home.admin', compact('item','kategori', 'produk','products', 'supplier', 'member', 'awal', 'akhir', 'data_pendapatan', 'data_tanggal','data'));
        else return view('home.kasir', compact('setting','products','item'));
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
}
