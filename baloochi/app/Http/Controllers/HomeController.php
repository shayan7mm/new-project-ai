<?php

namespace App\Http\Controllers;

use App\Services\CatalogService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(private readonly CatalogService $catalog)
    {
    }

    public function index(): View
    {
        return view('public.price-list', [
            'data' => $this->catalog->publicData(),
        ]);
    }
}
