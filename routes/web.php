    <?php

    /*
    |--------------------------------------------------------------------------
    | Web Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register web routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | contains the "web" middleware group. Now create something great!
    |
    */

    // Route::get('/', function () {
    //     return view('welcome');
    // });

    Route::get('/', 'HomeController@index')->name('home');
    Route::get('/home', 'HomeController@index');

    Auth::routes();

Route::group(['middleware' => 'auth'], function(){
    Route::get('user/profil', 'UserController@profil')->name('user.profil');
    Route::patch('user/{id}/change', 'UserController@changeProfil');

    Route::get('transaksi/mytransaction', 'PenjualanController@mytransaction')->name('transaksi.mytransaction');
    Route::get('transaksi/mytransaction_details', 'PenjualanController@listData_mytransaction')->name('penjualan.mytransaction_details');
    Route::get('transaksi/baru', 'PenjualanDetailController@newSession')->name('transaksi.new');
    Route::get('transaksi/{id}/data', 'PenjualanDetailController@listData')->name('transaksi.data');
    Route::get('transaksi/{id}', 'PenjualanDetailController@index')->name('transaksi.id');
    Route::get('transaksi/cetaknota', 'PenjualanDetailController@printNota')->name('transaksi.cetak');
    Route::get('transaksi/notapdf/{id}', 'PenjualanDetailController@notaPDF')->name('transaksi.pdf');
    Route::post('transaksi/simpan', 'PenjualanDetailController@saveData')->name('transaksi.simpan');
    Route::get('transaksi/loadform/{diskon}/{total}/{diterima}/{id}', 'PenjualanDetailController@loadForm');
    Route::resource('transaksi', 'PenjualanDetailController');
    Route::delete('transaksi/penjualan/{id}','PenjualanController@destroy');
    Route::get('member/data', 'MemberController@listData')->name('member.data');
    Route::get('produk/getjson', 'ProdukController@getJSON')->name('produk.getjson');
    Route::post('member/cetak', 'MemberController@printCard');
    Route::resource('member', 'MemberController');
    Route::get('penginapan/data', 'PenginapanController@listData')->name('penginapan.data');
    Route::resource('penginapan', 'PenginapanController');

});

Route::group(['middleware' => ['web', 'cekuser:1', 'auth' ]], function(){
    Route::get('kategori/data', 'KategoriController@listData')->name('kategori.data');
    Route::resource('kategori', 'KategoriController');

    Route::get('produk/data', 'ProdukController@listData')->name('produk.data');
    Route::post('produk/hapus', 'ProdukController@deleteSelected');
    Route::post('produk/cetak', 'ProdukController@printBarcode');
    Route::resource('produk', 'ProdukController');

    Route::get('supplier/data', 'SupplierController@listData')->name('supplier.data');
    Route::resource('supplier', 'SupplierController');

    // Route::get('member/data', 'MemberController@listData')->name('member.data');
    // Route::post('member/cetak', 'MemberController@printCard');
    // Route::resource('member', 'MemberController');

    Route::get('pengeluaran/data', 'PengeluaranController@listData')->name('pengeluaran.data');
    Route::resource('pengeluaran', 'PengeluaranController');


    Route::get('user/data', 'UserController@listData')->name('user.data');
    Route::resource('user', 'UserController');

    Route::get('pembelian/data', 'PembelianController@listData')->name('pembelian.data');
    Route::get('pembelian/{id}/tambah', 'PembelianController@create');
    Route::get('pembelian/{id}/lihat', 'PembelianController@show');
    Route::resource('pembelian', 'PembelianController');   

    Route::get('pembelian_detail/{id}/data', 'PembelianDetailController@listData')->name('pembelian_detail.data');
    Route::get('pembelian_detail/loadform/{diskon}/{total}', 'PembelianDetailController@loadForm');
    Route::get('pembelian_detail/loadform/{total}', 'PembelianDetailController@loadForm');
    Route::resource('pembelian_detail', 'PembelianDetailController');   

    Route::get('data_penjualan/{datefrom}/{dateto}/{product}', 'PenjualanController@listData')->name('penjualan.data');
    Route::get('penjualan/{id}/lihat', 'PenjualanController@show');
    Route::resource('penjualan', 'PenjualanController');
    Route::delete('penjualan','PenjualanController@destroy')->name('penjualan.delete');

    Route::get('laporan', 'LaporanController@index')->name('laporan.index');
    Route::get('laporan/profit_loss', 'LaporanController@profitloss')->name('laporan.profit_loss');
    Route::get('laporan/profitloss_data/{datefrom}/{dateto}/{product}', 'LaporanController@profitloss_data')->name('laporan.profit_loss.data');
    Route::post('laporan', 'LaporanController@refresh')->name('laporan.refresh');
    Route::get('laporan/data/{awal}/{akhir}', 'LaporanController@listData')->name('laporan.data'); 
    Route::get('laporan/pdf/{awal}/{akhir}', 'LaporanController@exportPDF');

    Route::resource('setting', 'SettingController');
});

