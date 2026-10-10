<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactMessageRequest extends FormRequest
{
    /** Nama field jebakan bot (honeypot). Manusia tidak melihat field ini. */
    public const HONEYPOT = 'website';

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
            self::HONEYPOT => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama',
            'email' => 'email',
            'message' => 'pesan',
        ];
    }

    public function isSpam(): bool
    {
        return filled($this->input(self::HONEYPOT));
    }
}
