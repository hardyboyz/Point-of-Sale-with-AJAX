<?php

namespace App\Http\Controllers;

use App\Kategori;
use Datatables;
use PDF;
use App\Produk;
use App\PembelianDetail;
use App\PenjualanDetail;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $kategori = Kategori::all();      
        return view('produk.index', compact('kategori'));
    }

    public function listData()
    {
        $produk = Produk::leftJoin('kategori', 'kategori.id_kategori', '=', 'produk.id_kategori')
        ->orderBy('produk.id_produk', 'desc')
        ->get();
            $no = 0;
            $data = array();
            foreach($produk as $list){
            $no ++;
            $row = array();
            $row[] = "<input type='checkbox' name='id[]'' value='".$list->id_produk."'>";
            $row[] = $no;
            $row[] = $list->kode_produk;
            $row[] = $list->nama_produk;
            $row[] = $list->nama_kategori;
            $row[] = $list->merk;
            $row[] = "Rp. ".format_uang($list->harga_beli);
            $row[] = "Rp. ".format_uang($list->harga_jual);
            //$row[] = $list->diskon."%";
            $row[] = $list->stok;
            $row[] = "<div class='btn-group'>
                    <a onclick='editForm(".$list->id_produk.")' class='btn btn-primary btn-sm'><i class='fa fa-pencil'></i></a>
                    <a onclick='deleteData(".$list->id_produk.")' class='btn btn-danger btn-sm'><i class='fa fa-trash'></i></a></div>";
            $data[] = $row;
            }
            
            $output = array("data" => $data);
            return response()->json($output);
            // return Datatables::of($data)->escapeColumns([])->make(true);
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
        $jml = Produk::where('kode_produk', '=', $request['kode'])->count();
        if($jml < 1){
            $produk = new Produk;
            $produk->kode_produk     = $request['kode'];
            $produk->nama_produk    = $request['nama'];
            $produk->id_kategori    = $request['kategori'];
            $produk->merk          = $request['merk'];
            $produk->harga_beli      = $request['harga_beli'];
            //$produk->diskon       = $request['diskon'];
            $produk->harga_jual    = $request['harga_jual'];
            $produk->stok          = $request['stok'];
            $produk->save();
            echo json_encode(array('msg'=>'success'));
        }else{
            echo json_encode(array('msg'=>'error'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Produk  $produk
     * @return \Illuminate\Http\Response
     */
    public function show(Produk $produk)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Produk  $produk
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $produk = Produk::find($id);
        echo json_encode($produk);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Produk  $produk
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $produk = Produk::find($id);
        $produk->nama_produk    = $request['nama'];
        $produk->id_kategori    = $request['kategori'];
        $produk->merk          = $request['merk'];
        $produk->harga_beli      = $request['harga_beli'];
        //$produk->diskon       = $request['diskon'];
        $produk->harga_jual    = $request['harga_jual'];
        $produk->stok          = $request['stok'];
        $produk->update();
        echo json_encode(array('msg'=>'success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Produk  $produk
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $produk = Produk::find($id);
        $produk->delete();
    }

    public function deleteSelected(Request $request)
    {
        foreach($request['id'] as $id){
            $produk = Produk::find($id);
            $produk->delete();
        }
    }

    public function printBarcode(Request $request)
    {
        $dataproduk = array();
        foreach($request['id'] as $id){
            $produk = Produk::find($id);
            $dataproduk[] = $produk;
        }
        $no = 1;
        $pdf = PDF::loadView('produk.barcode', compact('dataproduk', 'no'));
        $pdf->setPaper('a4', 'potrait');      
        return $pdf->stream();
    }

    public function getJSON(Request $request){

        ## Read value
        $draw = $request->get('draw') ?? 1;
        $start = $request->get("start") ?? 1;
        $rowperpage = $request->get("length") ?? 10; // Rows display per page
   
        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');
   
        // $columnIndex = $columnIndex_arr[0]['column']; // Column index
        // $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        // $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        // $searchValue = $search_arr['value']; // Search value
   
        // Total records
        $totalRecords = Produk::select('count(*) as allcount')->count();
        $totalRecordswithFilter = Produk::select('count(*) as allcount')->count();
   
        // Fetch records
        // $records = Product::orderBy($columnName,$columnSortOrder)
        //   ->where('produk.nama_product', 'like', '%' .$searchValue . '%')
        //   ->select('produk.*')
        //   ->skip($start)
        //   ->take($rowperpage)
        //   ->get();
        $records = Produk::select('produk.*');

        if(isset($columnIndex_arr)){
            $columnIndex = $columnIndex_arr[0]['column'];
            $columnName = $columnName_arr[$columnIndex]['data'];
            $records = $records->orderBy($columnName,$columnSortOrder);
        }

        if(isset($searchValue)){
            $records = $records->where('produk.nama_produk', 'like', '%' .$searchValue . '%');
            $totalRecordswithFilter = Produk::select('count(*) as allcount')->where('nama_produk', 'like', '%' .$searchValue . '%')->count();
        }

        $records = $records->select('produk.*')
        ->skip($start)
        ->take($rowperpage)
        ->get();
   
        $data_arr = [];
        
        foreach($records as $record){
           $id = $record->id;
           $nama_produk = $record->nama_produk;
           $kode_produk = $record->kode_produk;
           $harga_jual = $record->harga_jual;
           $stok = $this->getStock($record->kode_produk);
   
           $data_arr[] = array(
             "id" => $id,
             "nama_produk" => $nama_produk,
             "kode_produk" => $kode_produk,
             "harga_jual" => number_format($harga_jual),
             "stok" => $stok
           );
        }
   
        $response = array(
           "draw" => intval($draw),
           "iTotalRecords" => $totalRecords,
           "iTotalDisplayRecords" => $totalRecordswithFilter,
           "data" => $data_arr
        );
   
        echo json_encode($response);
        exit;
      }

      public function getStock($kode_produk){
        $stockIn = PembelianDetail::where('kode_produk',$kode_produk)->sum('jumlah');
        $stockOut = PenjualanDetail::where('kode_produk',$kode_produk)->sum('jumlah');
        return $stockIn - $stockOut;
    }
}
