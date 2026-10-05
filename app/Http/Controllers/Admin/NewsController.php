<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * Display news management page.
     */
    public function index()
    {
        return view('admin.news.index');
    }
}
