<?php

namespace App\Http\Requests;

use App\Models\Resource;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreResourceCommentRequest extends FormRequest
{
    /**
     * Any authenticated user, including the resource's own owner, can
     * comment on an approved resource.
     */
    public function authorize(): bool
    {
        $resource = $this->route('resource');

        return $this->user() !== null
            && $resource instanceof Resource
            && $resource->isApproved();
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:2000'],
        ];
    }
}
