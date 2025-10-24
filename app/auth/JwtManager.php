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
        // Clé secrète hardcodée (à changer en production pour plus de sécurité)
        $this->secretKey = 'my-super-secret-jwt-key-2024-florian';
        $this->algorithm = 'HS256';
        $this->expirationTime = 3600 * 24; // 24 heures
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

        error_log("Generating token with secret key (first 10 chars): " . substr($this->secretKey, 0, 10));
        return JWT::encode($payload, $this->secretKey, $this->algorithm);
    }

    public function validateToken(string $token): ?array
    {
        try {
            error_log("Validating token with secret key (first 10 chars): " . substr($this->secretKey, 0, 10));
            $decoded = JWT::decode($token, new Key($this->secretKey, $this->algorithm));
            error_log("Token validated successfully");
            return (array) $decoded;
        } catch (ExpiredException $e) {
            error_log("Token expired: " . $e->getMessage());
            return null; //Token expiré
        } catch (SignatureInvalidException $e) {
            error_log("Invalid signature: " . $e->getMessage());
            return null; //Signature invalide
        } catch (Exception $e) {
            error_log("Token validation error: " . $e->getMessage());
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