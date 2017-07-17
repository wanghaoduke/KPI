@extends('layouts.newApp')

@section('content')
    <div ng-app="adminApp">
        <div  ng-view ></div>
    </div>

    <script src="{{ asset('js/admin/app.js') }}"></script>
    <script src="{{ asset('js/admin/adminRoute.js') }}"></script>
    <script src="{{ asset('js/admin/AdminManageModel.js') }}"></script>
    <script src="{{ asset('js/admin/controllers/AdminIndexController.js') }}"></script>
@endsection