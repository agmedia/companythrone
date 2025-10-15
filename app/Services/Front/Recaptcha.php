<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Recaptcha
{
    private string $remote_ip;
    private string $verify_url;
    private ?object $result = null;

    public function __construct()
    {
        $this->remote_ip  = request()->ip() ?? ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
        $this->verify_url = config('services.recaptcha.verify_url');
    }

    /**
     * Provjeri recaptchu i spremi rezultat.
     */
    public function check(array $data): self|false
    {
        if (!isset($data['recaptcha'])) {
            return false;
        }

        try {
            $response = Http::asForm()->timeout(5)->post($this->verify_url, [
                'secret'   => config('services.recaptcha.secret'),
                'response' => $data['recaptcha'],
                'remoteip' => $this->remote_ip,
            ]);

            if (!$response->successful()) {
                Log::channel('recaptcha_error')->error('ReCaptcha HTTP greška', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return false;
            }

            $this->result = (object) $response->json();
        } catch (\Throwable $e) {
            Log::channel('recaptcha_error')->error('ReCaptcha iznimka', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
            return false;
        }

        return $this;
    }

    /**
     * Provjeri rezultat.
     */
    public function ok(string $expectedAction = null): bool
    {
        if (empty($this->result?->success)) {
            Log::channel('recaptcha_warning')->warning('ReCaptcha neuspješan odgovor', ['result' => $this->result]);
            return false;
        }

        // opcionalno: provjera akcije (ako je front šalje)
        if ($expectedAction && ($this->result->action ?? null) !== $expectedAction) {
            Log::channel('recaptcha_warning')->warning('ReCaptcha action mismatch', [
                'expected' => $expectedAction,
                'got'      => $this->result->action ?? null,
            ]);
            return false;
        }

        // loš score
        if (($this->result->score ?? 0) < 0.3) {
            Log::channel('recaptcha_warning')->warning('ReCaptcha loš score', [
                'score' => $this->result->score ?? 0,
                'threshold' => 0.3,
            ]);
            return false;
        }

        return true;
    }
}
