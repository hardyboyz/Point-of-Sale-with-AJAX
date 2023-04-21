@extends('layouts.app')

@section('title')
  Daftar Penjualan
@endsection

@section('breadcrumb')
   @parent
   <li>penjualan</li>
@endsection



@section('content')     
<div class="row">
  <div class="col-xs-12">
    <div class="box">
      <div class="box-body">  
      <div class="col-md-2">
        <label for="datefrom" class="form-label">Date From</label>
        <input type="text" class="form-control datepicker" id="datefrom" value="{{ date('Y-m-d') }}">
      </div>
      <div class="col-md-2">
        <label for="dateto" class="form-label">Date To</label>
        <input type="text" class="form-control datepicker" id="dateto" value="{{ date('Y-m-d') }}">
      </div>
      <div class="col-md-3">
        <label for="produk" class="form-label">Produk</label>
        <input type="text" class="form-control" id="product" value="-">
      </div>
      <div class="col-md-3" style="margin-top:22px">
        <label for="produk" class="form-label"></label>
        <button class="btn btn-success" id="search">Search</button>
      </div>
</div>
<div class="box-body table-responsive">

      <table class="table table-striped tabel-penjualan table-bordered">
      <thead>
        <tr>
            <th width="30">No</th>
            <th>Tanggal</th>
            <th>Member</th>
            <th>Produk</th>
            <th style="white-space:nowrap">Total Harga</th>
            <th>Diskon</th>
            <th>Total Bayar</th>
            <th>Kasir</th>
            <th width="100">Aksi</th>
        </tr>
      </thead>
      <tbody></tbody>
      <tfoot>
          <tr>
              <th colspan="6" style="text-align:right">Total:</th>
              <th></th>
          </tr>
      </tfoot>
      </table>

      </div>
    </div>
  </div>
</div>

@include('penjualan.detail')
@endsection

@section('script')
<script type="text/javascript">
var table, save_method, table1;

$(document).ready(function(){
  $('#search').click(function(){
      table.ajax.url("{{ url('data_penjualan') }}/"+$('#datefrom').val()+"/"+$('#dateto').val()+"/"+$('#product').val());
      table.ajax.reload();
   });
});

$(function(){
       
  datefrom = $('#datefrom').val();
  dateto = $('#dateto').val();
  product = $('#product').val();

   table = $('.tabel-penjualan').DataTable({
     "dom":"bfrtip",
     "processing" : true,
     "serverside" : true,
     "bFilter": false,
     "pageLength" : -1,
     "ajax" : {
       "url" : "{{ url('data_penjualan') }}/"+datefrom+"/"+dateto+"/"+product,
       "type" : "GET"
     },
     "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            // Total over this page
            pageTotal = api
                .column( 6, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Update footer
            $( api.column( 6 ).footer() ).html(
                '<span class="label label-success" style="font-size:1.4em"> Rp. '+pageTotal.toString().replace(/\./g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ".")+'</span>'
            );
        }
   }); 

   
   
   table1 = $('.tabel-detail').DataTable({
     "dom" : 'Brt',
     "bSort" : false,
     "processing" : true,
     "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            // Total over this page
            pageTotal = api
                .column( 5, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Update footer
            $( api.column( 5 ).footer() ).html(
                '<span class="label label-success" style="font-size:1.4em"> Rp. '+pageTotal.toString().replace(/\./g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ".")+'</span>'
            );
        }
    });

   $('.tabel-supplier').DataTable();

   $('.datepicker').datepicker({format:'yyyy-mm-dd','autoclose':true});

});

function addForm(){
   $('#modal-supplier').modal('show');        
}

function showDetail(id){
    $('#modal-detail').modal('show');

    table1.ajax.url("penjualan/"+id+"/lihat");
    table1.ajax.reload();
}

function deleteData(id){
   if(confirm("Apakah yakin data akan dihapus?")){
     $.ajax({
       url : "{{ route('penjualan.delete') }}/"+id,
       type : "POST",
       data : {'_method' : 'DELETE', '_token' : $('meta[name=csrf-token]').attr('content')},
       success : function(data){
         table.ajax.reload();
       },
       error : function(){
         alert("Tidak dapat menghapus data!");
       }
     });
   }
}
</script>
@endsection