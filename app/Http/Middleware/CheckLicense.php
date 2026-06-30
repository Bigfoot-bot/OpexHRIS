<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckLicense
{
    private const EXPECTED = '80c3fc40c9e0a71ab1c2d2ce35779bb3321ccc5279a107cb84dfe5b79e5024e0';

    public function handle(Request $request, Closure $next): Response
    {
        $key = env('APP_LICENSE_KEY', '');

        if (!hash_equals(self::EXPECTED, hash('sha256', $key))) {
            return response(self::lockHtml(), 503, ['Content-Type' => 'text/html']);
        }

        return $next($request);
    }

    public static function lockHtml(): string
    {
        return <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>License Not Activated</title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body {
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
                    background: #0f172a;
                    color: #e2e8f0;
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                .card {
                    background: #1e293b;
                    border: 1px solid #334155;
                    border-radius: 16px;
                    padding: 48px 56px;
                    max-width: 480px;
                    text-align: center;
                    box-shadow: 0 25px 50px rgba(0,0,0,0.5);
                }
                .icon {
                    width: 64px;
                    height: 64px;
                    background: #1d3557;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin: 0 auto 24px;
                    font-size: 28px;
                }
                h1 {
                    font-size: 22px;
                    font-weight: 700;
                    color: #f1f5f9;
                    margin-bottom: 12px;
                }
                p {
                    font-size: 14px;
                    color: #94a3b8;
                    line-height: 1.7;
                    margin-bottom: 8px;
                }
                .contact {
                    margin-top: 28px;
                    padding: 16px;
                    background: #0f172a;
                    border-radius: 10px;
                    font-size: 13px;
                    color: #64748b;
                }
                .contact a { color: #38bdf8; text-decoration: none; }
            </style>
        </head>
        <body>
            <div class="card">
                <div class="icon">&#128274;</div>
                <h1>License Not Activated</h1>
                <p>This software requires a valid license key to operate.</p>
                <p>Please complete your payment to receive your activation key.</p>
                <div class="contact">
                    Contact your provider to activate this system.<br>
                    <a href="mailto:hello@lyriops.com">hello@lyriops.com</a>
                    &nbsp;&bull;&nbsp;
                    <a href="https://wa.me/254719390538">WhatsApp +254 719 390 538</a>
                </div>
            </div>
        </body>
        </html>
        HTML;
    }
}
