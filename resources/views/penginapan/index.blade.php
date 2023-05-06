@extends('layouts.app')

@section('title')
  Data Penginapan Kucing HYPetshop
@endsection

@section('breadcrumb')
   @parent
   <li>member</li>
@endsection

@section('content')     
<div class="row">
  <div class="col-xs-12">
    <div class="box">
      <div class="box-header">
        <a onclick="penginapanForm()" class="btn btn-success btn-lg"><i class="fa fa-plus-circle"></i> Tambah</a>
        <!-- <a onclick="printCard()" class="btn btn-info"><i class="fa fa-credit-card"></i> Cetak Kartu</a> -->
      </div>
      <div class="box-body"> 
         <form method="post" id="form-penginapan">
         {{ csrf_field() }}
         <table class="table table-striped">
         <thead>
            <tr>
               <th width="20"><input type="checkbox" value="1" id="select-all"></th>
               <th width="20">No</th>
               <th>Member</th>
               <th>Nama Kucing</th>
               <th>Jml Kucing</th>
               <th>Tgl Masuk</th>
               <th>Tgl Keluar</th>
               <th>Kandang?</th>
               <th>Biaya</th>
               <th>Hari</th>
               <th>Total</th>
               <th>Keterangan</th>
               <th width="100">Aksi</th>
            </tr>
         </thead>
         <tbody></tbody>
         </table>
         </form>
      </div>
    </div>
  </div>
</div>
@include ('penginapan.penginapan')
@endsection

@section('script')
<script type="text/javascript">
var table, save_method, result, member;

$('#penginapan-form').on('shown.bs.modal', function(e) {
    $('.datetimepicker').datetimepicker({
      format: "Y-MM-DD H:MM",
    });

  });

$(function(){

   table = $('.table').DataTable({
     "processing" : true,
     "ajax" : {
       "url" : "{{ route('penginapan.data') }}",
       "type" : "GET"
     },
     'columnDefs': [{
         'targets': 0,
         'searchable': false,
         'orderable': false
      }],
      'order': [1, 'asc']
   }); 

   $('#select-all').click(function(){
      $('input[type="checkbox"]').prop('checked', this.checked);
   });

});

$('#penginapan-form form').validator().on('submit', function(e){
  if(!e.isDefaultPrevented()){
      var id = $('#id').val();
      if(save_method == "add") url = "{{ route('penginapan.store') }}";
      else url = "penginapan/"+id;
      
      $.ajax({
        url : url,
        type : "POST",
        data : $('#penginapan-form form').serialize(),
        dataType: 'JSON',
        success : function(data){
        if(data.msg=="error"){
          alert('Tidak bisa menyimpan data.!');
          $('#kode').focus().select();
        }else{
          $('#penginapan-form').modal('hide');
          table.ajax.reload();
        }
        },
        error : function(){
          alert("Tidak dapat menyimpan data!");
        }   
      });
      return false;
  }
});

function penginapanForm(id){
   save_method = "add";
   $('input[name=_method]').val('POST');
   $('#penginapan-form').modal('show');
   $('#penginapan-form form')[0].reset();            
   $('.penginapan-title').text('Tambah Penginapan Kucing');
}

function editForm(id){
   save_method = "edit";
   $('input[name=_method]').val('PATCH');
   $('#penginapan-form form')[0].reset();

   getData(id);
   getMember(result.member_id);
    $('#penginapan-form').modal('show');
    $('.modal-title').text('Edit Penginapan Kucing');
    $('#id').val(result.id);
    $('#nama_kucing').val(result.nama_kucing);
    $('#jml_kucing').val(result.jml_kucing);
    $('#tgl_masuk').val(result.tgl_masuk);
    $('#tgl_keluar').val(result.tgl_keluar);
    $('#keterangan').val(result.keterangan);
    $('#kandang').val(result.kandang);

    //setTimeout(function(){
      $('#member_id').val(result.member_id);
      $('#member_name').val(member.nama + " - " + member.telpon);
    //},2000);
}

function deleteData(id){
   if(confirm("Apakah yakin data akan dihapus?")){
     $.ajax({
       url : "penginapan/"+id,
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

function getData(id){
  $.ajax({
     url : "penginapan/"+id,
     type : "GET",
     dataType : "JSON",
     async : false,
     success : function(data){
       result = data;
     },
     error : function(err){
       //alert("Tidak dapat menampilkan data!");
       result = "error";
     }
   });
   return result;
}
function getMember(id){
  $.ajax({
     url : "member/"+id+"/edit",
     type : "GET",
     dataType : "JSON",
     async : false,
     success : function(data){
       member = data;
     },
     error : function(err){
       //alert("Tidak dapat menampilkan data!");
       result = "error";
     }
   });
   return result;
}

</script>
@endsection