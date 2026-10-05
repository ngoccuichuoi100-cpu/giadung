@extends('layouts.admin')

@section('title', 'Quản lý ưu đãi - GiaDụngPro')
@section('title_header', 'Quản lý ưu đãi')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[calc(100vh-11rem)] p-6">
    <p class="text-2xl font-semibold text-slate-700 text-center max-w-3xl leading-relaxed">
        Tính năng này đang được nâng cấp để mang lại trải nghiệm tốt hơn. Vui lòng quay lại sau nhé!
    </p>
    <img src="{{ asset('images/maintenance-bear.png') }}" alt="Tính năng đang được nâng cấp" class="w-72 sm:w-80 h-auto mt-6 pointer-events-none select-none drop-shadow-sm">
</div>
@endsection
