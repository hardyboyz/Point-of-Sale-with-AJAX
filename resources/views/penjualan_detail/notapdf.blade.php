<!DOCTYPE html>
<html>
<head>
   <title>Struk</title>
   <style type="text/css">
      table td {font: arial;font-size:x-small}
      table th{
         text-align: center;
      }
      table.data{ border-collapse: portrait }

   </style>
</head>
<body onload="goBack()" onafterprint="window.location='{{ $url }}'">
<div style="margin-left:-10px">
<table width="100%" style="text-align:center;margin:0">
  <tr>
     <td rowspan="3" width="120%"><img src="{{ url('/images/'.$setting->logo) }}" width="160px"><br>
     <span style="font-size:7px">{{ date('l, d-m-Y H:i') }}</span>
     <hr/>
     {{--<td>Kode Member</td>
     <td>: {{ $penjualan->kode_member }}</td>--}}
  </tr>
</table>
         
<table width="100%" style="border:solid 1px #111">
 
   <?php $diskon = 0; ?>
    @foreach($detail as $data)
    <?php $diskon+= $data->diskon; ?>
    <tr>
       <td style="width:40%">{{ $data->nama_produk }}</td>
       <td align="center"><span style="font-weight:bold">{{ $data->jumlah }}</span></td>
       <td align="left">{{ format_uang($data->sub_total) }}</td>
    </tr>
    @endforeach
   
    <tr><td colspan="1" align="right">SubTotal</td><td align="right"><b>{{ format_uang($penjualan->total_harga ?? 0) }}</b></td></tr>
    <tr><td colspan="1" align="right">Diskon</td><td align="right"><b>{{ format_uang($diskon) }}</b></td></tr>
    <tr><td colspan="1" align="right">Total</td><td align="right"><b>{{ format_uang($penjualan->bayar ?? 0) }}</b></td></tr>
    <tr><td colspan="1" align="right">Diterima</td><td align="right"><b>{{ format_uang($penjualan->diterima ?? 0) }}</b></td></tr>
    <tr><td colspan="1" align="right">Kembali</td><td align="right"><b>{{ format_uang(($penjualan->diterima ?? 0) - ($penjualan->bayar ?? 0)) }}</b></td></tr>

</table>

<table width="100%">
  <tr>
    <td align="center">
      <b>~Makase Yeee~ {{ $member->nama ?? '' }}<br/> <span style="text-decoration:underline">Follow IG @HYPETSHOPBELITONG</span></b>
    </td>
    <td align="center">
      <!--{{ Auth::user()->name}}-->
    </td>
  </tr>
</table>
   </div>
  
</body>
</html>

<script type="text/javascript">
   function goBack(){
	   window.print();
	   setTimeout(function(){
		   window.location='{{ $url }}'
	   },2000);
   }
   </script>
