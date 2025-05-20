<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NYTBestSellersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // I'm assuming this is a public API and that we don't need to
        // check for authorization.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'published_date' => 'sometimes|date_format:Y-m-d',
            'author' => 'sometimes|string|max:255',
            'isbn' => 'sometimes|array',
            'title' => 'sometimes|string|max:255',
        ];
    }
}
