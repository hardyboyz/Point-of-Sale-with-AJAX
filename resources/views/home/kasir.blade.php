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
  <div class="col-xs-12">
    <div class="box">
       <div class="box-body text-center">
            <h1>Hellow {{ Auth::user()->name }}, Welcome.</h1>
            <br><br>
            <a class="btn btn-success btn-lg" href="{{ route('transaksi.new') }}">New Transaction</a>
            <br><br><br>
      </div>
    </div>
    <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Run Out Stock</h3>
        </div>
        <div class="box-body">
          <ul class="list-group">
         {{-- @foreach($products as $p)
          @php $item++ @endphp
              @if($p->stok < 5 || $p->nama_produk != null)
              @php $color = $item %2 != 0 ? 'warning' : 'info' @endphp
              <div class="col-sm-3">
                  <li class="list-group-item list-group-item-{{$color}}">{{ $p->nama_produk }} : {{ $p->stok }}</li>
              </div>
              @endif
          @endforeach --}}
          </ul>
        </div>
    </div>
  </div>
</div>
@endsection