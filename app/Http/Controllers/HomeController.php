<?php

namespace App\Http\Controllers;

use App\Support\MockCatalog;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        // Fase 3: data sementara. Fase 5 → ganti dengan query Product.
        return view('home', [
            'products' => MockCatalog::all(),
        ]);
    }
}
