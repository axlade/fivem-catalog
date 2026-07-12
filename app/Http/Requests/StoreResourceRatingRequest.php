<?php

namespace App\Http\Requests;

use App\Models\Resource;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreResourceRatingRequest extends FormRequest
{
    /**
     * Any authenticated user can rate an approved resource they don't own.
     */
    public function authorize(): bool
    {
        $resource = $this->route('resource');

        return $this->user() !== null
            && $resource instanceof Resource
            && $resource->isApproved()
            && $resource->user_id !== $this->user()->id;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'between:1,5'],
        ];
    }
}
