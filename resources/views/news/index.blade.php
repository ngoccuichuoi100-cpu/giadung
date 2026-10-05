@extends('layouts.client')

@section('title', 'Tin tức & Sự kiện - GiaDụngPro')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[calc(100vh-20rem)] py-16 px-4 text-center">
    <p class="text-2xl font-semibold text-slate-700 dark:text-slate-200 max-w-3xl leading-relaxed">
        Tính năng này đang được nâng cấp để mang lại trải nghiệm tốt hơn. Vui lòng quay lại sau nhé!
    </p>
    <img src="{{ asset('images/maintenance-bear.png') }}" alt="Tính năng đang được nâng cấp" class="w-72 sm:w-80 h-auto mt-6 pointer-events-none select-none drop-shadow-sm">
</div>
@endsection
