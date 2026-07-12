<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateResourceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $resource = $this->route('resource');

        return $resource && ($this->user()?->can('update', $resource) ?? false);
    }

    /**
     * Split the newline-separated showcase videos textarea into an array.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'video_urls' => collect(preg_split('/\r\n|\r|\n/', (string) $this->input('video_urls', '')))
                ->map(fn (string $line) => trim($line))
                ->filter()
                ->values()
                ->all(),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $resource = $this->route('resource');
        $isFree = (float) $this->input('price', 0) === 0.0;
        $isFileMethod = $isFree && $this->input('download_method') === 'file';
        $isLinkMethod = $isFree && ! $isFileMethod;
        $needsNewFile = $isFileMethod && ! $resource->hasDownloadFile();

        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'video_urls' => ['nullable', 'array', 'max:5'],
            'video_urls.*' => ['url', 'max:2048'],
            'category' => ['required', Rule::in(['scripts', 'mlos', 'eup', 'vehicles'])],
            'framework' => ['required', Rule::in(['esx', 'qb-core', 'standalone', 'ox'])],
            'price' => ['required', 'numeric', 'min:0'],
            'download_method' => ['nullable', Rule::in(['link', 'file'])],
            'download_url' => [Rule::requiredIf($isLinkMethod), 'nullable', 'url', 'max:2048'],
            'download_file' => [Rule::requiredIf($needsNewFile), 'nullable', 'file', 'mimes:zip', 'max:51200'],
            'tebex_url' => [Rule::requiredIf(! $isFree), 'nullable', 'url', 'max:2048'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'images' => ['nullable', 'array', 'max:8'],
            'images.*' => ['image', 'max:4096'],
            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['integer', Rule::exists('resource_images', 'id')->where('resource_id', $resource->id)],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'download_url.required' => 'A GitHub / direct download URL is required for free resources.',
            'download_file.required' => 'A ZIP file is required when hosting the download directly on FiveM-Catalog.',
            'download_file.mimes' => 'The download must be a ZIP archive.',
            'tebex_url.required' => 'A Tebex store URL is required for paid resources.',
            'video_urls.max' => 'You can add up to 5 showcase videos.',
            'video_urls.*.url' => 'Each showcase video must be a valid URL.',
            'images.max' => 'You can add up to 8 additional showcase images.',
        ];
    }
}
