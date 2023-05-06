@extends('layouts.app')

@section('title')
  Transaksi Penjualan
@endsection

@section('breadcrumb')
   @parent
   <li>penjualan</li>
   <li>tambah</li>
@endsection

@section('content')     
<div class="row">
  <div class="col-xs-12">
    <div class="box">
   
      <div class="box-body">

<form class="form form-horizontal form-produk" method="post">
{{ csrf_field() }}  
  <input type="hidden" name="idpenjualan" value="{{ $idpenjualan ?? '' }}">
  <div class="form-group">
      <label for="kode" class="col-md-2 control-label">Kode Produk</label>
      <div class="col-md-5">
        <div class="input-group">
          <input id="kode" type="text" class="form-control" name="kode" autofocus required>
          <span class="input-group-btn">
            <button onclick="showProduct()" type="button" class="btn btn-info">...</button>
          </span>
        </div>
      </div>
  </div>
</form>

<form class="form-keranjang">
{{ csrf_field() }} {{ method_field('PATCH') }}
<table class="table table-striped tabel-penjualan table-responsive table-bordered table-hover">
<thead>
   <tr>
      <th width="30">No</th>
      <th>Kode Produk</th>
      <th>Nama Produk</th>
      <th align="right">Harga</th>
      <th>Jumlah</th>
      <th>Jumlah Hari</th>
      <th>Diskon</th>
      <th align="right">Sub Total</th>
      <th>Aksi</th>
   </tr>
</thead>
<tbody></tbody>
</table>
</form>

  <div class="col-md-8">
     <div id="tampil-bayar" style="background: #dd4b39; color: #fff; font-size: 80px; text-align: center; height: 120px"></div>
     <div id="tampil-terbilang" style="background: #3c8dbc; color: #fff; font-size: 25px; padding: 10px"></div>
  </div>
  <div class="col-md-4">
    <form class="form form-horizontal form-penjualan" method="post" action="{{ route('transaksi.simpan') }}">
      {{ csrf_field() }}
      <input type="hidden" name="idpenjualan" value="{{ $idpenjualan ?? '' }}">
      <input type="hidden" name="total" id="total">
      <input type="hidden" name="totalitem" id="totalitem">
      <input type="hidden" name="bayar" id="bayar">

      <div class="form-group">
        <label for="totalrp" class="col-md-4 control-label">Total</label>
        <div class="col-md-8">
          <input type="text" class="form-control" id="totalrp" readonly>
        </div>
      </div>

      <div class="form-group">
        <label for="member" class="col-md-4 control-label">Kode Member</label>
        <div class="col-md-8">
          <div class="input-group">
            <input id="membername" type="text" class="form-control" name="membername" value="0">
            <input id="member" type="hidden" name="member">
            <span class="input-group-btn">
              <button onclick="showMember()" type="button" class="btn btn-info">...</button>
            </span>
          </div>
        </div>
      </div>

      <!--<div class="form-group">
        <label for="diskon" class="col-md-4 control-label">Diskon</label>
        <div class="col-md-8">
          <input type="text" class="form-control" name="diskon" id="diskon" value="0">
        </div>
      </div>-->

      <div class="form-group">
        <label for="bayarrp" class="col-md-4 control-label">Jumlah</label>
        <div class="col-md-8">
          <input type="text" class="form-control" id="bayarrp" readonly style="font-size:2em;color:red">
        </div>
      </div>

      <div class="form-group">
        <label for="diterima" class="col-md-4 control-label">Diterima</label>
        <div class="col-md-8">
          <input type="text" class="form-control" onkeydown="thousand_separator(this.value)" value="0" name="diterima" id="diterima" style="font-size:2em">
        </div>
        
      </div>

      <div class="form-group">
        <label for="kembali" class="col-md-4 control-label">Kembali</label>
        <div class="col-md-8">
          <input type="text" class="form-control" id="kembali" value="0" readonly style="font-size:2em" name="kembali">
        </div>
      </div>

    </form>
    
    
      
  </div>
<div class="box-footer btn-group" style="float:right">
        <button class="btn btn-warning btn-lg" id="btnUangPas">UANG PAS</button>
        <button class="btn btn-primary btn-lg" id="btn20rb">20.000</button>
        <button class="btn btn-primary btn-lg" id="btn50rb">50.000</button>
        <button class="btn btn-primary btn-lg" id="btn100rb">100.000</button>
        <!-- <button class="btn btn-primary btn-lg" id="btn200rb">200.000</button>
        <button class="btn btn-primary btn-lg" id="btn300rb">300.000</button> -->
        <button type="button" class="btn btn-danger btn-lg simpan"><i class="fa fa-floppy-o"></i> SIMPAN</button>
        {{--<a target="_blank" href="{{ url('transaksi/notapdf/'.$idpenjualan) }}" class="btn btn-success btn-lg pull-right simpan"><i class="fa fa-print"></i> PRINT </a> --}}
      </div>
      </div>
      
      
    </div>
  </div>
</div>
@include('penjualan_detail.produk')
@include('penjualan_detail.member')
@endsection

@section('script')
<script type="text/javascript">
  // $(".content-wrapper:not(#diterima)").click(function(){
  //   $('#kode').focus();
  // });

  function rewrite_button(bayar){
    var zero = bayar.length;
    divider = 1;
    if(zero === 4) divider = 1000;
    if(zero === 5) divider = 10000;
    if(zero === 6) divider = 100000;

    total = Math.ceil(bayar/divider) * divider;
    ttl_button = total;

    btn50rb = total + 20000;
    ttl_button50rb = Math.round(btn50rb/10000) * 10000;

    btn100rb = total + 50000;
    ttl_button100rb = Math.round(btn100rb/10000) * 10000;

    //console.log(ttl_button)


    $('#btn20rb').val(ttl_button);
    $('#btn20rb').html(thousand_separator(ttl_button));

    $('#btn50rb').val(ttl_button50rb);
    $('#btn50rb').html(thousand_separator(ttl_button50rb));

    $('#btn100rb').val(ttl_button100rb);
    $('#btn100rb').html(thousand_separator(ttl_button100rb));

    //$('#btn50rb').val(Math.round(ttl));
    //$('#btn50rb').html(Math.round(ttl));

    $('#btnUangPas').val(bayar);
  }

var table;
$(function(){

  $('#btnUangPas').click(function(){
    $('#diterima').val($('#bayar').val());
    hitungKembalian($('#diskon').val(), $('#diterima').val());
  });

  $('#btn20rb').click(function(){
    $('#diterima').val($(this).val());
    hitungKembalian($('#diskon').val(), $('#diterima').val());
  });

  $('#btn50rb').click(function(){
    $('#diterima').val($(this).val());
    hitungKembalian($('#diskon').val(), $('#diterima').val());
  });

  $('#btn100rb').click(function(){
    $('#diterima').val($(this).val());
    hitungKembalian($('#diskon').val(), $('#diterima').val());
  });

  $('.tabel-produk').DataTable();

  // $('#table_produk').DataTable({
  //    "dom" : 'Brt',
  //    "bSort" : false,
  //    "processing" : true,
  //    "pageLength" : "10",
  //    "ajax" : {
  //      "url" : "{{ route('produk.getjson') }}",
  //      "type" : "GET"
  //    },
  //    columns: [
  //         {
  //             data: "kode_produk",
  //         },
  //         {
  //             data: "nama_produk"
  //         },
  //         {
  //             data: "harga_jual"
  //         },
  //         {
  //             data: "stok"
  //         },
  //         {
  //             data: "kode_produk",
  //             render: function (data) {
  //                 return '<a onclick="selectItem()" class="btn btn-primary"><i class="fa fa-check-circle"></i> Pilih</a>';
  //             }
  //         }
  //     ]
  // });

  table = $('.tabel-penjualan').DataTable({
     "dom" : 'Brt',
     "bSort" : false,
     "processing" : true,
     "pageLength" : "1000",
     "ajax" : {
       "url" : "{{ route('transaksi.data', $idpenjualan ?? '') }}",
       "type" : "GET"
     }
  }).on('draw.dt', function(){
    loadForm($('#diskon').val(),$('#diterima').val());
  });

   $('.form-produk').on('submit', function(){
      return false;
   });

   $('body').addClass('sidebar-collapse');

   $('#kode').change(function(){
      addItem();
   });

   $('.form-keranjang').submit(function(){
     return false;
   });

   $('#member').change(function(){
      //selectMember($(this).val());
   });

   $('#diterima').focusout(function(){
      var diterima = parseInt(remove_separator($('#diterima').val()));
      var bayar = parseInt($('#bayar').val());
     if(diterima < bayar){
       swal('','Pembayaran kurang','error');
       $('#kode').focus();
     }

     if(bayar <= 0){
       swal('','No Action','error');
       $('#kode').focus();
     }
   });


   $('#diterima').keyup(function(){
      if($(this).val() == "") $(this).val(0).select();
      $(this).val(thousand_separator($(this).val()));

      hitungKembalian($('#diskon').val(), $(this).val());
   }).focus(function(){
      $(this).select();
   });
  

   $('.simpan').click(function(){
     var diterima = parseInt(remove_separator($('#diterima').val()));
     var bayar = parseInt($('#bayar').val());
     if(diterima < bayar){
       swal('','Pembayaran kurang','error');
       return false;
     }else{
      $('.form-penjualan').submit();
     }
   });

   $('.rowdiskon').focus(function(){
    $(this).select();
   });

});

function addItem(){
  $.ajax({
    url : "{{ route('transaksi.store') }}",
    type : "POST",
    data : $('.form-produk').serialize(),
    success : function(data){
      $('#kode').val('').focus();
      table.ajax.reload(function(){
         loadForm($('#diskon').val(),$('#diterima').val());
      });             
    },
    error : function(err){
      console.log(err);
      alert("Tidak dapat menyimpan data!");
    }   
  });
}

function showProduct(){
  $('#modal-produk').modal('show');
}

function showMember(){
  $('#modal-member').modal('show');
}

function selectItem(kode){
  $('#kode').val(kode);
  $('#modal-produk').modal('hide');
  addItem();
}

function changeCount(id){
     $.ajax({
        url : "{{ URL('transaksi') }}/"+id,
        type : "POST",
        data : $('.form-keranjang').serialize(),
        success : function(data){
          $('#kode').focus();
          table.ajax.reload(function(){
            loadForm($('#diskon').val(),remove_separator($('#diterima').val()));
          });            
        },
        error : function(err){
          console.log(err)//alert("Tidak dapat menyimpan data!");
        }
     });
      
    //  var kembali = remove_separator($('#diterima').val()) - $('#bayar').val();
    //  console.log(kembali)
    //  $("#kembali").val('test');
    //$('#diterima').keyup();
}

function selectMember(kode,nama){
  $('#modal-member').modal('hide');
  $('#diskon').val('{{ $setting->diskon_member }}');
  $('#member').val(kode);
  $('#membername').val(nama);
  //loadForm($('#diskon').val());
  //$('#diterima').val(0).focus().select();
}

function deleteItem(id){
   if(confirm("Apakah yakin data akan dihapus?")){
     $.ajax({
       url : "{{ URL('transaksi') }}/"+id,
       type : "POST",
       data : {'_method' : 'DELETE', '_token' : $('meta[name=csrf-token]').attr('content')},
       success : function(data){
         table.ajax.reload(function(){
            loadForm($('#diskon').val());
            $('#kode').focus();
          }); 
       },
       error : function(){
         alert("Tidak dapat menghapus data!");
       }
     });
   }
}

function hitungKembalian(diskon=0, diterima=0){
  var total = $('#total').val($('.total').text());
  $('#totalitem').val($('.totalitem').text());

  $('#tampil-bayar').html("<small>Bayar:</small>"+$('#bayarrp').val());
  $('#tampil-terbilang').text(terbilang($('#bayar').val()));

  total = parseInt($('#bayar').val());
  terima = parseInt(remove_separator($('#diterima').val()));

  kembali = terima - total;

  // if(kembali < 0){
  //   swal('','Uang yang diterima kurang dari total pembayaran.','error');
  // }

  //$('#kembali').val("Rp. "+thousand_separator(kembali));
  //$('#diterima').val(thousand_separator(terima));
  if(remove_separator($('#diterima').val()) != 0){
    $('#tampil-bayar').html("<small>Kembali:</small> Rp. "+thousand_separator(kembali));
    $('#kembali').val("Rp. "+thousand_separator(kembali));
    $('#tampil-terbilang').text(terbilang(kembali));
  }
}

function loadForm(diskon=0, diterima=0){
  var total = $('#total').val($('.total').text());
  //console.log(total)
  // if(total == '') total = 0;
  $('#totalitem').val($('.totalitem').text());

  $.ajax({
       url : '{{ URL("transaksi/loadform") }}/'+diskon+"/"+ $('#total').val()+"/"+remove_separator(diterima)+"/"+'{{ $idpenjualan ?? "" }}',
       type : "GET",
       dataType : 'JSON', 
       success : function(data){
         //alert(data.totalrp);
         $('#totalrp').val("Rp. "+data.totalrp);
         //$('#diterima').val("Rp. "+data.totalrp);
         $('#bayarrp').val("Rp. "+data.bayarrp);
         $('#bayar').val(data.bayar);
         $('#bayar').val(data.bayar);
         $('#tampil-bayar').html("<small>Bayar:</small> Rp. "+data.bayarrp);
         $('#tampil-terbilang').text(data.terbilang);
        
         $('#kembali').val("Rp. "+data.kembalirp);

         rewrite_button(data.bayar);
         //$('#diterima').val(thousand_separator(data.diterima));
         if(remove_separator($('#diterima').val()) != 0){
            $('#tampil-bayar').html("<small>Kembali:</small> Rp. "+data.kembalirp);
            $('#tampil-terbilang').text(data.kembaliterbilang);
         }
       },
       error : function(){
         alert("Tidak dapat menampilkan data!");
       }
  });
}

$('#modal-produk').on("hidden.bs.modal", function(){
  $('#kode').focus();
})

function thousand_separator(x){
    if(typeof x !== 'undefined') {
        return x.toString().replace(/\./g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
}

function remove_separator(x){
    if(typeof x !== 'undefined') {
        return x.toString().replace(/\./g, '');
    }
}

</script>

@endsection