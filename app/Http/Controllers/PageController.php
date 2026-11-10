<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/** Halaman informasi publik (statis). */
class PageController extends Controller
{
    public function ai(): View { return view('pages.ai'); }
    public function faq(): View { return view('pages.faq'); }
    public function guide(): View { return view('pages.guide'); }
    public function about(): View { return view('pages.about'); }
    public function articles(): View { return view('pages.articles'); }
}
