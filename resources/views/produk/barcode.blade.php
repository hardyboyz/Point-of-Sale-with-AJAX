<!DOCTYPE html>
<html>
<head>
   <title>Cetak Barcode</title>
</head>
<body>
   <table width="100%">   
     <tr>
      {{--@if(count($dataproduk) == 1)
         @for($i = 1; $i<=24; $i++)--}}
            @foreach($dataproduk as $data)
            <td align="center" style="border: 1px solid #ccc">
            <span style="font-size:small">{{ $data->nama_produk}} - Rp. {{ format_uang($data->harga_jual) }}</span><br><br/>
            <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG( $data->kode_produk, 'C39') }}" height="60" width="180">
            {{ $data->kode_produk}}
            <br/>
            </td>
               @if( $no++ % 3 == 0)
                  </tr><tr>
               @endif
            @endforeach
        {{-- @endfor
      @endif   --}}  
     </tr>
   </table>
</body>
</html>