<?php



namespace App\Http\Controllers;

use Redirect;
use App\Supplier;
use App\PembelianDetail;
use App\Produk;
use App\Pembelian;
use Illuminate\Http\Request;

ini_set("date_default_time_zone",'Asia/Jakarta');

class PembelianController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $supplier = Supplier::all();
        return view('pembelian.index', compact('supplier'));       
    }

    public function listData()
    {
    
        $pembelian = Pembelian::leftJoin('supplier', 'supplier.id_supplier', '=', 'pembelian.id_supplier')
            ->orderBy('pembelian.id_pembelian', 'desc')
            ->get(['supplier.nama','pembelian.*','pembelian.created_at as transaction_date']);
        $no = 0;
        $data = array();
        foreach($pembelian as $list){
            $no ++;
            $row = array();
            $row[] = $no;
            $row[] = date('d-m-Y H:i', strtotime($list->created_at));
            $row[] = $list->nama;
            $row[] = $list->total_item;
            $row[] = "Rp. ".format_uang($list->total_harga);
            //$row[] = $list->diskon."%";
            $row[] = "Rp. ".format_uang($list->bayar);
            $row[] = '<div class="btn-group">
                    <a onclick="detail('.$list->id_pembelian.')" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i></a>
                    <a onclick="deleteData('.$list->id_pembelian.')" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>
                </div>';
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
    public function create($id)
    {
        $pembelian = new Pembelian;
        $pembelian->id_supplier = $id;     
        $pembelian->total_item = 0;     
        $pembelian->total_harga = 0;     
        $pembelian->diskon = 0;     
        $pembelian->bayar = 0;     
        $pembelian->save();

        session(['idpembelian' => $pembelian->id_pembelian]);
        session(['idsupplier' => $id]);

        return Redirect::route('pembelian_detail.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd($request);
        $pembelian = Pembelian::find($request['idpembelian']);
        $pembelian->total_item = $request['totalitem'];
        $pembelian->total_harga = $request['total'];
        $pembelian->diskon = $request['diskon'];
        $pembelian->bayar = $request['bayar'];
        $pembelian->update();

        $detail = PembelianDetail::where('id_pembelian', '=', $request['idpembelian'])->get();
        foreach($detail as $data){
            $produk = Produk::where('kode_produk', '=', $data->kode_produk)->first();
            $produk->stok += $data->jumlah;
            $produk->update();
        }
        return Redirect::route('pembelian.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Pembelian  $pembelian
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $detail = PembelianDetail::leftJoin('produk', 'produk.kode_produk', '=', 'pembelian_detail.kode_produk')
        ->where('id_pembelian', '=', $id)
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
            $row[] = "Rp. ".format_uang($list->harga_beli);
            $row[] = $list->jumlah;
            $row[] = "Rp. ".format_uang($list->harga_beli * $list->jumlah);
            $data[] = $row;
            $total+= ($list->harga_beli * $list->jumlah);
        }
        
        $output = array("data" => $data,'total'=>$total);
        return response()->json($output);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Pembelian  $pembelian
     * @return \Illuminate\Http\Response
     */
    public function edit(Pembelian $pembelian)
    {

        return Redirect::route('pembelian_detail.edit',compact('pembelian'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Pembelian  $pembelian
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pembelian $pembelian)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Pembelian  $pembelian
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $pembelian = Pembelian::find($id);
        $pembelian->delete();

        $detail = PembelianDetail::where('id_pembelian', '=', $id)->get();
        foreach($detail as $data){
            $produk = Produk::where('kode_produk', '=', $data->kode_produk)->first();
            $produk->stok -= $data->jumlah;
            $produk->update();
            $data->delete();
        }
    }
}
