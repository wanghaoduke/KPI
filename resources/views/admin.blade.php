@extends('layouts.newApp')

@section('content')
    <div ng-app="adminApp">
        <div  ng-view ></div>
    </div>

    <script src="{{ asset('angular-ui-select/dist/select.js') }}"></script>
    <script src="{{ asset('js/admin/app.js') }}"></script>
    <script src="{{ asset('js/admin/adminRoute.js') }}"></script>
    <script src="{{ asset('js/admin/AdminManageModel.js') }}"></script>
    <script src="{{ asset('js/admin/controllers/AdminIndexController.js') }}"></script>
    <link href="{{ asset('angular-ui-select/dist/select.css') }}" rel="stylesheet" type="text/css" >
@endsection