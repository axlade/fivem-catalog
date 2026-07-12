<?php

namespace App\Http\Requests;

use App\Models\Resource;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreResourceUpdateRequest extends FormRequest
{
    /**
     * Only the resource's owner (or an admin) can publish a changelog entry.
     */
    public function authorize(): bool
    {
        $resource = $this->route('resource');

        return $resource instanceof Resource && ($this->user()?->can('update', $resource) ?? false);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
        ];
    }
}
