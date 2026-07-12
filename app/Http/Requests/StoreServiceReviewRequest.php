<?php

namespace App\Http\Requests;

use App\Models\Service;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreServiceReviewRequest extends FormRequest
{
    /**
     * Any authenticated user can review an active service they don't own.
     */
    public function authorize(): bool
    {
        $service = $this->route('service');

        return $this->user() !== null
            && $service instanceof Service
            && $service->is_active
            && $service->user_id !== $this->user()->id;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
