@extends('layouts.app')

@section('title', 'Dashboard - Test Php Laravel')

@section('styles')
    @livewireStyles
@endsection

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
       @livewire('navbar')
       <h3>Dashboard</h3>
    </div>
    <div class="card-header d-flex justify-content-between align-items-center">

    </div>
    <div class="card-body">
        @livewire('brewery-list')
    </div>
</div>
@endsection

@section('scripts')
    @livewireScripts
@endsection
