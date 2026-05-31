<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

use App\Enums\FigurinhaCategoria;
use Illuminate\Validation\Rules\Enum;

class StoreFigurinhaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
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
            'titulo' => ['required', 'string', 'min:3', 'max:60'],
            'arquivo' => ['required', 'file', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120'],
            'categoria' => ['required', new Enum(FigurinhaCategoria::class)],
            'tags' => ['nullable', 'string', 'max:255'], // As tags podem vir como string separada por vírgula no form
        ];
    }
}
