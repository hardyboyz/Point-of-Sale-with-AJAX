<div class="modal" id="penginapan-form" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
     
   <form class="form-horizontal" data-toggle="validator" method="post">
   {{ csrf_field() }} {{ method_field('POST') }}
   
   <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"> &times; </span> </button>
      <h3 class="modal-title"></h3>
   </div>
            
<div class="modal-body">
   <input type="hidden" id="member_id" name="member_id">
   <div class="form-group">
      <label for="nama" class="col-md-3 control-label">Nama Pemilik</label>
      <div class="col-md-6">
         <input id="member_name" type="text" class="form-control" name="member_name" autofocus required>
         <span class="help-block with-errors"></span>
      </div>
   </div>

   <div class="form-group">
      <label for="alamat" class="col-md-3 control-label">Nama Kucing</label>
      <div class="col-md-8">
         <input id="nama_kucing" type="text" class="form-control" name="nama_kucing" required>
         <span class="help-block with-errors"></span>
      </div>
   </div>

   <div class="form-group">
      <label for="telpon" class="col-md-3 control-label">Tanggal Masuk</label>
      <div class="col-md-6">
         <input id="tgl_masuk" type="text" value="{{ $date }}" class="form-control datepicker" name="tgl_masuk" autofocus required>
         <span class="help-block with-errors"></span>
      </div>
   </div>
   <div class="form-group">
      <label for="telpon" class="col-md-3 control-label">Kandang ?</label>
      <div class="col-md-6">
         <select name="kandang" id="kandang">
            @foreach($penginapan as $p)
               <option value="{{ $p->id_produk }}"> {{ $p->nama_produk }} => {{ $p->harga_jual }}</option>
            @endforeach
         </select>

         <span class="help-block with-errors"></span>
      </div>
   </div>
   
</div>
   
   <div class="modal-footer">
      <button type="submit" class="btn btn-primary btn-save"><i class="fa fa-floppy-o"></i> Simpan </button>
      <button type="button" class="btn btn-warning" data-dismiss="modal"><i class="fa fa-arrow-circle-left"></i> Batal</button>
   </div>
      
   </form>

         </div>
      </div>
   </div>
