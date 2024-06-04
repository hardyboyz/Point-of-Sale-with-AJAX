@extends('layouts.app')

@section('title')
  Transaksi Penjualan
@endsection

@section('breadcrumb')
   @parent
   <li>penjualan</li>
@endsection

@section('content')     
<div class="row">
  <div class="col-xs-12">
    <div class="box">
      <div class="box-body table-responsive">  

<table class="table table-striped tabel-penjualan table-hovered display table-bordered">
<thead>
   <tr>
      <th width="30">No</th>
      <th>Tanggal</th>
      <th>Member</th>
      <th>Products</th>
      <th>Total Harga</th>
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
        <th class="badge badge-success" style="font-size:1.4em;background-color:#00a65a"></th>
    </tr>
</tfoot>
</table>

      </div>
    </div>
  </div>
</div>

@endsection

@section('script')
<script type="text/javascript">
var table, save_method, table1;
$(function(){
  var selected = [];
   table = $('.tabel-penjualan').DataTable({
     "processing" : true,
     "serverside" : true,
     "pageLength" : 100,
     "ajax" : {
       "url" : "{{ route('penjualan.mytransaction_details') }}",
       "type" : "GET",
     },
     "rowCallback": function( row, data ) {
            if ( $.inArray(data.DT_RowId, selected) !== -1 ) {
                $(row).addClass('selected');
            }
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

          // Total over all pages
          total = api
              .column( 6 )
              .data()
              .reduce( function (a, b) {
                  return intVal(a) + intVal(b);
              }, 0 );

          // Total over this page
          pageTotal = api
              .column( 6, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                  return intVal(a) + intVal(b);
              }, 0 );

          // Update footer
          $( api.column( 6 ).footer() ).html(
              'Rp '+number_format(pageTotal)
          );
      }
   }); 

   $('.tabel-penjualan tbody').on('click', 'tr', function () {
        var id = this.id;
        var index = $.inArray(id, selected);
 
        if ( index === -1 ) {
            selected.push( id );
        } else {
            selected.splice( index, 1 );
        }
 
        $(this).toggleClass('selected');
    } );
   
   table1 = $('.tabel-detail').DataTable({
     "dom" : 'Brt',
     "bSort" : false,
     "processing" : true,
    });

   $('.tabel-supplier').DataTable();
});

function deleteData(id){
  alert('Transaksi tidak bisa dibatalkan');
   //if(confirm("Apakah yakin data akan dihapus?")){
    // $.ajax({
    //   url : "penjualan/"+id,
    //   type : "POST",
    //   data : {'_method' : 'DELETE', '_token' : $('meta[name=csrf-token]').attr('content')},
     //  success : function(data){
    //     table.ajax.reload();
    //   },
     //  error : function(){
     //    alert("Tidak dapat menghapus data!");
     //  }
    // });
   //}
}

function getMoney(data){
  var matches = data.match(/(\d+)/);
    
  if (matches) {
      return matches[0];
  }
}

function printNota(id){

}

function number_format(nStr)
{
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + '.' + '$2');
    }
    return x1 + x2;
}
</script>
@endsection