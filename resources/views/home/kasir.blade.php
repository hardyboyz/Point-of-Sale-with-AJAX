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
  </div>
</div>
@endsection