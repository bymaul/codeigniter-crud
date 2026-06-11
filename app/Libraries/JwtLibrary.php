<?php

namespace App\Libraries;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtLibrary
{
    private static string $secret;
    private static int $expiry = 86400;

    private static function getSecret(): string
    {
        if (empty(self::$secret)) {
            self::$secret = env('JWT_SECRET', 'this-is-a-very-secret-key-change-me-please');
        }
        return self::$secret;
    }

    public static function generateToken(array $payload): string
    {
        $payload = array_merge($payload, [
            'iat' => time(),
            'exp' => time() + self::$expiry,
            'iss' => base_url(),
        ]);

        return JWT::encode($payload, self::getSecret(), 'HS256');
    }

    public static function decodeToken(string $token): ?object
    {
        try {
            return JWT::decode($token, new Key(self::getSecret(), 'HS256'));
        } catch (\Exception $e) {
            return null;
        }
    }
}
