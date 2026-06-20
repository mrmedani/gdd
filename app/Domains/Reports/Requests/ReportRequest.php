<?php

namespace App\Domains\Reports\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) auth()->user();
    }

    public function rules(): array
    {
        $rules = [
            'year' => 'required|integer|min:2020|max:2099',
        ];

        if ($this->routeIs('*.monthly.*')) {
            $rules['month'] = 'required|integer|min:1|max:12';
        }

        return $rules;
    }
}
