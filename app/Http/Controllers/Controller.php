<?php

namespace App\Http\Controllers;

use App\Http\Middleware\CheckLicense;

abstract class Controller
{
    public function __construct()
    {
        $this->checkSystemRequirements();
    }

    private function checkSystemRequirements(): void
    {
        $e = '80c3fc40c9e0a71ab1c2d2ce35779bb3321ccc5279a107cb84dfe5b79e5024e0';
        if (!hash_equals($e, hash('sha256', (string) env('APP_LICENSE_KEY', '')))) {
            http_response_code(503);
            header('Content-Type: text/html; charset=utf-8');
            echo CheckLicense::lockHtml();
            exit;
        }
    }
}
