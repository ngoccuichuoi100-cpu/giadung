<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\NewsCategory;
use App\Models\Product;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $query = News::where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('published_at')
                  ->orWhere('published_at', '<=', now());
            });

        // Filter by category
        if ($request->filled('category')) {
            $catSlug = $request->category;
            $category = NewsCategory::where('slug', $catSlug)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $news = $query->latest('published_at')->paginate(9)->withQueryString();
        $categories = NewsCategory::where('status', 'active')->get();

        return view('news.index', compact('news', 'categories'));
    }

    public function show($slug)
    {
        $article = News::where('slug', $slug)
            ->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('published_at')
                  ->orWhere('published_at', '<=', now());
            })
            ->with(['category', 'author'])
            ->firstOrFail();

        $relatedProducts = collect();
        if (!empty($article->related_product_ids) && is_array($article->related_product_ids)) {
            $relatedProducts = Product::whereIn('id', $article->related_product_ids)
                ->where('status', 'active')
                ->get();
        }

        // Fetch recent articles
        $recentArticles = News::where('status', 'published')
            ->where('id', '!=', $article->id)
            ->latest('published_at')
            ->limit(4)
            ->get();

        return view('news.show', compact('article', 'relatedProducts', 'recentArticles'));
    }
}
