<?php

namespace App\Support;

use Illuminate\Http\Request;

class ClientIpResolver
{
    public static function resolve(Request $request): ?string
    {
        $candidates = [];

        $forwardedFor = (string) $request->header('X-Forwarded-For', '');
        if ($forwardedFor !== '') {
            foreach (explode(',', $forwardedFor) as $item) {
                $candidates[] = trim($item);
            }
        }

        $candidates[] = (string) $request->header('CF-Connecting-IP', '');
        $candidates[] = (string) $request->header('X-Real-IP', '');
        $candidates[] = (string) $request->ip();
        $candidates[] = (string) $request->server('REMOTE_ADDR');

        $normalized = collect($candidates)
            ->map(fn (string $item) => self::normalize($item))
            ->filter(fn (?string $item) => $item !== null)
            ->values();

        $publicIp = $normalized->first(fn (string $ip) => filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        ) !== false);

        if (is_string($publicIp) && $publicIp !== '') {
            return $publicIp;
        }

        return $normalized->first();
    }

    private static function normalize(string $value): ?string
    {
        $candidate = trim($value, " \t\n\r\0\x0B\"'");
        if ($candidate === '') {
            return null;
        }

        if (str_starts_with(strtolower($candidate), 'for=')) {
            $candidate = trim(substr($candidate, 4), " \t\n\r\0\x0B\"'[]");
        }

        if (filter_var($candidate, FILTER_VALIDATE_IP) !== false) {
            return $candidate;
        }

        // Handle IPv4 format with port: "1.2.3.4:5678"
        if (preg_match('/^(\d{1,3}(?:\.\d{1,3}){3}):\d+$/', $candidate, $matches) === 1) {
            $ip = $matches[1];
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) {
                return $ip;
            }
        }

        return null;
    }
}

