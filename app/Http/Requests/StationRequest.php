<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $stations = [];

        foreach ($this->all() as $key => $value) {
            if (str_starts_with($key, 'stations-') && !empty($value)) {
                $stations[] = (int) $value;
            }
        }

        if (!empty($stations)) {
            $this->merge([
                'stations' => $stations,
            ]);
        }
    }


    public function rules(): array
    {
        return [
            'address' => 'required|string|min:10',
            'capacity' => 'required|integer',
            'stations' => 'nullable|array',
            'stations.*' => 'integer',
        ];
    }
}
