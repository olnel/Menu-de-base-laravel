<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected string $apiKey;

    protected string $senderId;

    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.befianasms.api_key');
        $this->senderId = config('services.befianasms.sender_id');
        $this->baseUrl = config('services.befianasms.url');
    }

    /**
     * Envoyer un SMS simple
     */
    public function sendSms(string $to, string $message, $sendAt = null): array
    {
        $url = $sendAt
            ? "{$this->baseUrl}/api/smsko/v1/sendlater/"
            : "{$this->baseUrl}/api/smsko/v1/send/";

        try {
            $payload = [
                'phone_number' => $this->formatPhoneNumber($to),
                'message' => $message,
                'sender' => $this->senderId,
                'senderName ' => $this->senderId,
            ];

            if ($sendAt !== null) {
                $payload['send_at'] = $sendAt instanceof \DateTime
                    ? $sendAt->format('Y-m-d H:i')
                    : (string) $sendAt;
            }

            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($url, $payload);

            $result = $response->json();

            // dd($url, $result);

            if ($response->successful()) {
                if (isset($result['message']) && str_contains(strtolower($result['message']), 'sent successfully')) {
                    return [
                        'success' => true,
                        'message' => $result['message'],
                        'data' => [
                            'address' => $result['address'] ?? null,
                            'clientCorrelator' => $result['clientCorrelator'] ?? null,
                            'callbackData' => $result['callbackData'] ?? null,
                        ],
                    ];
                }
            }

            return $this->formatErrorResponse($response, $result);

        } catch (\Exception $e) {
            Log::error('Erreur envoi SMS: '.$e->getMessage(), ['exception' => $e]);

            return [
                'success' => false,
                'message' => 'Erreur technique: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Envoyer un SMS à plusieurs destinataires
     */
    public function sendBulkSms(array $recipients, string $message, $sendAt = null): array
    {
        try {
            $formattedRecipients = array_map(fn ($recipient) => $this->formatPhoneNumber($recipient), $recipients);

            $payload = [
                'phone_numbers' => $formattedRecipients,
                'message' => $message,
                'sender' => $this->senderId,
            ];

            if ($sendAt !== null) {
                $payload['send_at'] = $sendAt instanceof \DateTime
                    ? $sendAt->format('Y-m-d H:i')
                    : (string) $sendAt;
            }

            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post("{$this->baseUrl}/api/smsko/v1/sendbulk/", $payload);

            $result = $response->json();

            if ($response->successful() && isset($result['message'])) {
                return [
                    'success' => true,
                    'message' => $result['message'],
                    'data' => $result,
                ];
            }

            return $this->formatErrorResponse($response, $result);

        } catch (\Exception $e) {
            Log::error('Erreur envoi SMS groupés: '.$e->getMessage(), ['exception' => $e]);

            return [
                'success' => false,
                'message' => 'Erreur technique: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Vérifier le solde SMS
     */
    public function getBalance(): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
                'Accept' => 'application/json',
            ])->get("{$this->baseUrl}/api/smsko/v1/balance/");

            $result = $response->json();

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Solde récupéré avec succès',
                    'balance' => $result['balance'] ?? 0,
                    'data' => $result,
                ];
            }

            return $this->formatErrorResponse($response, $result);

        } catch (\Exception $e) {
            Log::error('Erreur récupération solde SMS: '.$e->getMessage(), ['exception' => $e]);

            return [
                'success' => false,
                'message' => 'Erreur technique: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Formater les numéros de téléphone
     */
    protected function formatPhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '261')) {
            $phone = substr($phone, 3);
        }

        if (str_starts_with($phone, '0')) {
            $phone = substr($phone, 1);
        }

        if (! str_starts_with($phone, '3')) {
            $phone = '3'.$phone;
        }

        return $phone;
    }

    /**
     * Gérer et formater les erreurs API
     */
    protected function formatErrorResponse($response, array $result): array
    {
        $status = $response->status();
        $errorMessage = $result['error'] ?? $result['message'] ?? 'Erreur inconnue.';

        $messages = [
            400 => 'Requête invalide : vérifiez les paramètres envoyés.',
            401 => 'Clé API invalide ou manquante.',
            403 => 'Compte non activé ou solde insuffisant.',
            404 => 'Ressource introuvable.',
            422 => 'Erreur de validation des données.',
            429 => 'Trop de requêtes, veuillez réessayer plus tard.',
            500 => 'Erreur interne du serveur SMS.',
            503 => 'Service SMS temporairement indisponible.',
        ];

        return [
            'success' => false,
            'status' => $status,
            'message' => $messages[$status] ?? $errorMessage,
            'error' => $result,
        ];
    }
}
