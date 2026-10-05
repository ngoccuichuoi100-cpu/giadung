@extends('layouts.client')

@section('title', $article->title . ' - GiaDụngPro')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <!-- Breadcrumbs -->
        <nav class="flex text-xs text-slate-500 mb-6 gap-2 text-left" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-orange-500">Trang chủ</a>
            <span>/</span>
            <a href="{{ route('news.index') }}" class="hover:text-orange-500">Tin tức</a>
            <span>/</span>
            <span class="text-slate-800 dark:text-slate-200 font-medium truncate max-w-[200px]">{{ $article->title }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 items-start text-left">
            <!-- Left Area: Main Content -->
            <div class="lg:col-span-2 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-3xl p-8 shadow-sm">
                <!-- Meta information -->
                <div class="space-y-4">
                    <span class="bg-orange-100 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 text-xs font-bold px-3 py-1 rounded-full">
                        {{ $article->category ? $article->category->name : 'Tin tức' }}
                    </span>
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white leading-tight">
                        {{ $article->title }}
                    </h1>
                    <div class="flex items-center gap-4 text-xs text-slate-450 border-y border-slate-100 dark:border-slate-700 py-3">
                        <span>Tác giả: <span class="font-bold text-slate-700 dark:text-slate-350">{{ $article->author ? $article->author->name : 'GiaDụngPro' }}</span></span>
                        <span>•</span>
                        <span>📅 Xuất bản: {{ $article->published_at ? $article->published_at->format('d/m/Y H:i') : $article->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>

                <!-- Main article image -->
                @if($article->thumbnail)
                    <div class="mt-6 aspect-video rounded-2xl overflow-hidden border dark:border-slate-700 shadow-sm">
                        <img src="{{ $article->thumbnail_url }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
                    </div>
                @endif

                <!-- Article Content -->
                <div class="mt-8 text-sm text-slate-700 dark:text-slate-300 leading-relaxed space-y-6">
                    @if($article->summary)
                        <div class="p-4 bg-slate-50 dark:bg-slate-900/60 rounded-2xl border-l-4 border-orange-500 font-medium italic mb-6">
                            {{ $article->summary }}
                        </div>
                    @endif

                    <div class="prose max-w-none text-slate-650 dark:text-slate-300">
                        {!! nl2br(e($article->content)) !!}
                    </div>
                </div>

                <!-- Related Products Section -->
                @if($relatedProducts->isNotEmpty())
                    <div class="mt-12 border-t border-slate-100 dark:border-slate-700 pt-8">
                        <h3 class="font-bold text-base text-slate-800 dark:text-white mb-6">Sản phẩm liên quan trong bài viết</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            @foreach($relatedProducts as $prod)
                                @include('partials.product-card', ['product' => $prod])
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Area: Sidebar Recent Articles -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-3xl p-6 shadow-sm">
                    <h3 class="font-bold text-sm text-slate-800 dark:text-white border-b border-slate-100 dark:border-slate-700 pb-3 mb-4">Bài viết mới nhất</h3>
                    <div class="space-y-4">
                        @forelse($recentArticles as $recent)
                            <a href="{{ route('news.show', $recent->slug) }}" class="flex gap-3 group">
                                <div class="w-16 h-16 rounded-xl bg-slate-100 dark:bg-slate-900 overflow-hidden flex-shrink-0 border dark:border-slate-850">
                                    @if($recent->thumbnail)
                                        <img src="{{ $recent->thumbnail_url }}" alt="{{ $recent->title }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-400">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 group-hover:text-orange-500 transition-colors line-clamp-2">{{ $recent->title }}</h4>
                                    <span class="text-[10px] text-slate-400 block mt-1">📅 {{ $recent->published_at ? $recent->published_at->format('d/m/Y') : $recent->created_at->format('d/m/Y') }}</span>
                                </div>
                            </a>
                        @empty
                            <div class="text-center text-slate-400 text-xs py-4">Chưa có bài viết mới.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
