@extends('layouts.newApp')

@section('content')
    <div ng-app="advicesApp">
        <div ng-view ></div>
    </div>

    <link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://cdn.bootcss.com/jquery/2.1.1/jquery.min.js"></script>
    <script src="https://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="{{ asset('js/advices/app.js') }}"></script>
    <script src="{{ asset('js/advices/advicesRoute.js') }}"></script>
    <script src="{{ asset('js/advices/AdvicesModel.js') }}"></script>
    <script src="{{ asset('js/advices/controllers/AdvicesController.js') }}"></script>

@endsection