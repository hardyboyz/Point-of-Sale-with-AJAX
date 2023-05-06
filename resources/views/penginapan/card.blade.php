<!DOCTYPE html>
<html>
<head>
   <title>Cetak Kartu Member</title>

   <style>
     .box{position: relative;}
     .card{ width: 501.732pt; height: 147.402pt; }
     .kode{ 
        position: absolute; 
        top: 20pt; 
        left: 10pt; 
        color: #fff;
        font-size: 15pt;
      }
      .barcode{ 
        position: absolute; 
        top: 80pt; 
        left: 278pt; 
        font-size: 15pt;
      }
   </style>
</head>
<body>
   <table width="100%">      
    
    @foreach($datamember as $data)
    <tr>
      <td>
      <div class="box">
        <img src="{{ asset('images/card.png') }}" class="card">
        <div class="kode">
          <img src="data:image/png;base64,{{ \DNS1D::getBarcodePNG( $data->kode_member, 'C39') }}" height="30" width="130">
          <span style="color:#000">{{ $data->nama }}</span>
        </div>
        <div class="barcode">
        {{ $data->kode_member }} - {{ $data->nama }} <br/>
        <div style="font-size:x-small">{{ $data->alamat }}</div>
          <br><!--{{ $data->kode_member }}-->
        </div>
      </div>
      </td>
    </tr>
    @endforeach

   </table>
</body>
</html>