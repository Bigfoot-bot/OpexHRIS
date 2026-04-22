<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Tenant\IpWhitelist;

class CheckIpWhitelist
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $whitelist = IpWhitelist::where('tenant_id', tenant('id'))
                                    ->where('is_active', true)
                                    ->get();

            // If no IPs are whitelisted, allow all
            if ($whitelist->isEmpty()) {
                return $next($request);
            }

            $clientIp = $request->ip();

            // Check if client IP is in whitelist
            $allowed = $whitelist->contains(function ($item) use ($clientIp) {
                // Support CIDR notation
                if (str_contains($item->ip_address, '/')) {
                    return $this->ipInCidr($clientIp, $item->ip_address);
                }
                return $item->ip_address === $clientIp;
            });

            if (!$allowed) {
                abort(403, 'Access denied. Your IP address is not whitelisted.');
            }
        } catch (\Exception $e) {
            // If tenant context not available, allow through
        }

        return $next($request);
    }

    private function ipInCidr(string $ip, string $cidr): bool
    {
        [$subnet, $mask] = explode('/', $cidr);
        return (ip2long($ip) & ~((1 << (32 - $mask)) - 1)) === ip2long($subnet);
    }
}
