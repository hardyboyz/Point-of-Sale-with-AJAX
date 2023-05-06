@extends('layouts.app')

@section('title')
Profit and Loss
@endsection

@section('breadcrumb')
   @parent
   <li>Report</li>
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

      <table class="table table-striped tabel-profit-loss table-bordered">
      <thead>
        <tr>
            <th width="30">No</th>
            <th>Date</th>
            <th>Product</th>
            <th>Qty</th>
            <th style="white-space:nowrap">Total</th>
            <th>Discount</th>
            <th>Profit</th>
        </tr>
      </thead>
      <tbody></tbody>
      <tfoot>
          <tr>
             <th colspan="3" style="text-align:right">Total Items:</th>
              <th></th>
              <th colspan="2" style="text-align:right">Total Profit:</th>
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
      table.ajax.url("{{ url('laporan/profitloss_data') }}/"+$('#datefrom').val()+"/"+$('#dateto').val()+"/"+$('#product').val());
      table.ajax.reload();
   });
});

$(function(){
       
  datefrom = $('#datefrom').val();
  dateto = $('#dateto').val();
  product = $('#product').val();

    table = $('.tabel-profit-loss').DataTable({
     "dom":"bfrtip",
     "processing" : true,
     "serverside" : true,
     "bFilter": false,
     "pageLength" : -1,
     "ajax" : {
       "url" : "{{ url('laporan/profitloss_data') }}/"+datefrom+"/"+dateto+"/"+product,
       "type" : "GET"
     },
     "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            pageTotal = api
                .column( 6, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            $( api.column( 6 ).footer() ).html(
                '<span class="label label-success" style="font-size:1.4em"> Rp. '+pageTotal.toString().replace(/\./g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ".")+'</span>'
            );

            totalBeli = api
                .column( 3, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            $( api.column( 3 ).footer() ).html(
                '<span class="label label-success" style="font-size:1.4em">'+totalBeli.toString().replace(/\./g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ".")+'</span>'
            );
        }
   }); 

   $('.datepicker').datepicker({format:'yyyy-mm-dd','autoclose':true});

});

</script>
@endsection