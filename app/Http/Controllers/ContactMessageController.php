<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactMessageRequest;
use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Throwable;

/**
 * Form kontak Box, Box (PRD F8). Bekerja dengan dan tanpa JavaScript:
 * request biasa → redirect kembali ke #box-box, request fetch (Accept: application/json) → JSON.
 */
class ContactMessageController extends Controller
{
    /** Maksimal kiriman berhasil per IP dalam jendela waktu di bawah. */
    public const MAX_ATTEMPTS = 3;

    public const DECAY_SECONDS = 600; // 10 menit

    public const SUCCESS_MESSAGE = 'Pesan terkirim! Aku balas secepatnya.';

    public function store(StoreContactMessageRequest $request): JsonResponse|RedirectResponse
    {
        $key = 'contact:'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            $minutes = (int) ceil(RateLimiter::availableIn($key) / 60);

            throw ValidationException::withMessages([
                'message' => "Kamu sudah mengirim {$this->limitText()}. Coba lagi dalam {$minutes} menit, ya.",
            ]);
        }

        // Honeypot terisi = bot. Pura-pura sukses supaya bot tidak belajar, tapi tidak disimpan.
        if (! $request->isSpam()) {
            $message = ContactMessage::create([
                ...$request->safe()->only(['name', 'email', 'message']),
                'ip' => $request->ip(),
            ]);

            RateLimiter::hit($key, self::DECAY_SECONDS);

            $this->notifyOwner($message);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => self::SUCCESS_MESSAGE]);
        }

        return redirect()->to(url('/').'#box-box')->with('contact_sent', self::SUCCESS_MESSAGE);
    }

    /**
     * Kirim email ke Dun hanya jika mailer sungguhan sudah diatur. Kegagalan email
     * tidak boleh menggagalkan form: pesan sudah tersimpan dan bisa dibaca di admin.
     */
    protected function notifyOwner(ContactMessage $message): void
    {
        $recipient = setting('contact_email');

        if (blank($recipient) || in_array(config('mail.default'), ['log', 'array'], true)) {
            return;
        }

        try {
            Mail::to($recipient)->send(new ContactMessageReceived($message));
        } catch (Throwable $e) {
            report($e);
        }
    }

    protected function limitText(): string
    {
        return self::MAX_ATTEMPTS.' pesan dalam '.(self::DECAY_SECONDS / 60).' menit';
    }
}
