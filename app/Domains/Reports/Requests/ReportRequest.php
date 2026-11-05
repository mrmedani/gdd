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
        // year obligatoire pour mensuel/annuel ; nullable pour custom/share
        $customOrShare = $this->routeIs('*.custom.*') || $this->routeIs('*.share.*');
        $rules = [
            'year' => ($customOrShare ? 'nullable' : 'required') . '|integer|min:2020|max:2099',
            'category_id' => 'nullable|integer|exists:expense_categories,id',
            'employee_id' => 'nullable|integer|exists:employees,id',
            'payment_method' => 'nullable|string',
        ];

        if ($this->routeIs('*.monthly.*')) {
            $rules['month'] = 'required|integer|min:1|max:12';
        }

        // Rapport personnalisé : plage de dates libre (date_to >= date_from)
        if ($this->routeIs('*.custom.*')) {
            $rules['date_from'] = 'required|date';
            $rules['date_to'] = 'required|date|after_or_equal:date_from';
        }

        // Partage WhatsApp : kind du rapport
        if ($this->routeIs('*.share.*')) {
            $rules['kind'] = 'required|in:monthly,annual,custom';
            if ($this->input('kind') === 'monthly') {
                $rules['month'] = 'required|integer|min:1|max:12';
            }
            if ($this->input('kind') === 'custom') {
                $rules['date_from'] = 'required|date';
                $rules['date_to'] = 'required|date|after_or_equal:date_from';
            }
        }

        return $rules;
    }
}
