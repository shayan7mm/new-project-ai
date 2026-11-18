<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CatalogService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    /** دانلود نسخه پشتیبان JSON از تنظیمات، دسته‌ها و محصولات */
    public function __invoke(CatalogService $catalog): StreamedResponse
    {
        $json = json_encode($catalog->publicData(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $filename = 'baloochi-backup-'.now()->format('Y-m-d-His').'.json';

        return response()->streamDownload(fn () => print($json), $filename, [
            'Content-Type' => 'application/json; charset=utf-8',
        ]);
    }
}
