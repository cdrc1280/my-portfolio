<?php

declare(strict_types=1);

namespace App\Http\Requests\Public;

use App\Domain\Contact\DataTransferObjects\ContactMessageData;
use Illuminate\Foundation\Http\FormRequest;

class StoreContactMessageRequest extends FormRequest
{
    /**
     * Name of the hidden field humans never see or fill in.
     */
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
            'email' => ['required', 'email:rfc', 'max:150'],
            'subject' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            self::HONEYPOT => ['nullable', 'string', 'max:255'],
        ];
    }

    public function isSpam(): bool
    {
        return filled($this->input(self::HONEYPOT));
    }

    public function toData(): ContactMessageData
    {
        return ContactMessageData::fromValidated($this->validated(), $this->ip(), $this->userAgent());
    }
}
