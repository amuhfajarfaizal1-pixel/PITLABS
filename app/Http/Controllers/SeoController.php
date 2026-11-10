<?php

namespace App\Http\Controllers;

use App\Support\MockCatalog;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    /** sitemap.xml. Fase 5: ambil daftar produk dan artikel dari database. */
    public function sitemap(): Response
    {
        $urls = [
            [route('home'), '1.0'],
            [route('catalog.index'), '0.9'],
            [route('availability'), '0.6'],
            [route('guide'), '0.7'],
            [route('ai'), '0.4'],
            [route('faq'), '0.6'],
            [route('about'), '0.5'],
            [route('articles.index'), '0.5'],
        ];
        foreach (MockCatalog::all() as $p) {
            $urls[] = [route('catalog.show', $p['slug']), '0.8'];
        }

        $lastmod = now('Asia/Makassar')->toDateString();
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n".'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
        foreach ($urls as [$loc, $priority]) {
            $xml .= '  <url><loc>'.e($loc).'</loc><lastmod>'.$lastmod.'</lastmod><priority>'.$priority.'</priority></url>'."\n";
        }
        $xml .= '</urlset>'."\n";

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
