<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

class JwtManager
{
    private string $secretKey;
    private string $algorithm;
    private int $expirationTime;

    public function __construct() {
        $this->secretKey = $_ENV['JWT_SECRET'] ?? 'your-secret-key-change-this-in-production';
        $this->algorithm = 'HS256';
        $this->expirationTime = 3600 * 24;
    }

    public function generateToken(array $userData): string
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + $this->expirationTime;

        $payload = [
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'user_id' => $userData['id'],
            'email' => $userData['email'],
        ];

        return JWT::encode($payload, $this->secretKey, $this->algorithm);
    }

    public function validateToken(string $token): ?array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, $this->algorithm));
            return (array) $decoded;
        } catch (ExpiredException $e) {
            return null; //Token expiré
        } catch (SignatureInvalidException $e) {
            return null; //Signature invalide
        } catch (Exception $e) {
            return null; //Autre erreur
        }
    }

    public function extractTokenFromHeader(string $authHeader): ?string
    {
        if (strpos($authHeader, 'Bearer ') === 0) {
            return substr($authHeader, 7);
        }
        return null;
    }
}