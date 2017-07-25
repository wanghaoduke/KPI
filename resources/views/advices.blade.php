@extends('layouts.newApp')

@section('content')
    <div ng-app="advicesApp">
        <div ng-view ></div>
    </div>

    <script src="{{ asset('js/advices/app.js') }}"></script>
    <script src="{{ asset('js/advices/advicesRoute.js') }}"></script>
    <script src="{{ asset('js/advices/AdvicesModel.js') }}"></script>
    <script src="{{ asset('js/advices/controllers/AdvicesController.js') }}"></script>

@endsection