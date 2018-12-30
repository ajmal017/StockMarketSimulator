@extends('main')
@section('main')
<link rel="stylesheet" href="{{asset('css/flipclock.css')}}">
<meta name="_token" content="{{ csrf_token() }}">
  <div class="section" id="app">  
  	<trade-share :predata="{{$predata}}" ></trade-share>
</div>
@stop

<meta name="csrf-token" content="{{ csrf_token() }}">     
@section('scripts')  
  <script src="{{asset('js/flipclock.min.js')}}"></script>	
  <script src="{{asset('js/app.js')}}"></script>  	
  <script>window.Laravel = { csrfToken: '{{ csrf_token() }}' }</script>
  <!--<script src="{{asset('js/market.js')}}"></script>  -->
@stop