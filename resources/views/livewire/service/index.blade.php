@extends('layouts.service.home')
@section('content')
    <div class="grid gap-4">
        <livewire:service.products-pagination />
        <livewire:service.services-pagination />
        <livewire:service.packages-pagination />
    </div>
@endsection
