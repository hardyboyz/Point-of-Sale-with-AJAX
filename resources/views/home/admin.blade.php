@extends('layouts.app')

@section('title')
  Dashboard
@endsection

@section('breadcrumb')
   @parent
   <li>Dashboard</li>
@endsection

@section('content') 
<div class="row">
    <div class="col-lg-2 col-xs-6">
        <div class="small-box bg-aqua">
            <div class="inner">
            	<h3 style="font-size: 2em">{{ $data['sales_today'] }}</h3>
           		<p>Total Sales Today</p>
            </div>
       		<div class="icon">
            	<i class="fa fa-cube"></i>
        	</div>
        </div>
        <div class="small-box bg-aqua">
            <div class="inner">
            	<h3 style="font-size: 2em">{{ $data['sales_this_month'] }}</h3>
           		<p>Sales This Month</p>
            </div>
       		<div class="icon">
            	<i class="fa fa-cube"></i>
        	</div>
        </div>
    </div>
    <div class="col-lg-2 col-xs-6">
        <div class="small-box bg-yellow">
            <div class="inner">
            	<h3 style="font-size: 2em">{{ $data['profit_today'] }}</h3>
           		<p>Total Profit Today</p>
            </div>
       		<div class="icon">
            	<i class="fa fa-cube"></i>
        	</div>
        </div>
        <div class="small-box bg-yellow">
            <div class="inner">
            	<h3 style="font-size: 2em">{{ $data['profit_this_month'] }}</h3>
           		<p>Profit This Month</p>
            </div>
       		<div class="icon">
            	<i class="fa fa-cube"></i>
        	</div>
        </div>
    </div>
    <div class="col-lg-2 col-xs-6">
        <div class="small-box bg-aqua">
            <div class="inner">
            	<h3>{{ $kategori }}</h3>
           		<p>Total Kategori</p>
            </div>
       		<div class="icon">
            	<i class="fa fa-cube"></i>
        	</div>
        </div>
    </div>
    <div class="col-lg-2 col-xs-6">
        <div class="small-box bg-green">
            <div class="inner">
            	<h3>{{ $produk }}</h3>
           		<p>Total Produk</p>
            </div>
       		<div class="icon">
            	<i class="fa fa-cubes"></i>
        	</div>
        </div>
    </div>
    <div class="col-lg-2 col-xs-6">
        <div class="small-box bg-yellow">
            <div class="inner">
            	<h3>{{ $supplier }}</h3>
           		<p>Total Supplier</p>
            </div>
       		<div class="icon">
            	<i class="fa fa-truck"></i>
        	</div>
        </div>
    </div>
    <div class="col-lg-2 col-xs-6">
        <div class="small-box bg-red">
            <div class="inner">
            	<h3>{{ $member }}</h3>
           		<p>Total Member</p>
            </div>
       		<div class="icon">
            	<i class="fa fa-credit-card"></i>
        	</div>
        </div>
    </div>
</div>

{{--<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
            	<h3 class="box-title">Run Out Stock</h3>
            </div>
            <div class="box-body">
            	<div class="stock">
                    <table class="table table-border table-hover" id="runoutStock">
                        <tr>
                            <th>No.</th>
                            <th>Kategori</th>
                            <th>Nama Produk</th>
                            <th>Stok</th>
                        </tr>
                        @foreach($products as $p)
                        @php $item++ @endphp
                            @if($p->stok < 5 || $p->nama_produk != null)
                            @php $color = $item %2 != 0 ? 'warning' : 'info' @endphp
                            <tr>
                                <td>{{$item}}</td>
                                <td>{{ $p->nama_kategori }}</td>
                                <td>{{ $p->nama_produk }}</td>
                                <td>{{ $p->stok }}</td>
                            </tr>
                            @endif
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>--}}

<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
            	<h3 class="box-title">Graphic Sales. {{ date('l, d M Y', strtotime($awal)) }} &raquo; {{ date('l, d M Y', strtotime($akhir)) }}</h3>
            </div>
            <div class="box-body">
            	<div class="chart">
                    <canvas id="salesChart" style="height: 250px;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script type="text/javascript">
$(function () {
    $('#runoutStock').DataTable();

  var salesChartCanvas = $("#salesChart").get(0).getContext("2d");
  var salesChart = new Chart(salesChartCanvas);

  var salesChartData = {
    labels: {{ json_encode($data_tanggal) }},
    datasets: [
      {
        label: "HYPETSHOP MANGGAR",
        fillColor: "rgba(60,141,188,0.9)",
        strokeColor: "rgb(210, 214, 222)",
        pointColor: "rgb(210, 214, 222)",
        pointStrokeColor: "#c1c7d1",
        pointHighlightFill: "#fff",
        pointHighlightStroke: "rgb(220,220,220)",
        data: {{ json_encode($data_pendapatan) }}
      }
    ]
  };

  var salesChartOptions = {
    pointDot: false,
    responsive: true,
    tooltips: {
      callbacks: {
          label: function(tooltipItem, data) {
              return tooltipItem.yLabel.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
          }
      }
  }
  };

  //Create the line chart
  salesChart.Line(salesChartData, salesChartOptions);
});
</script>
@endsection