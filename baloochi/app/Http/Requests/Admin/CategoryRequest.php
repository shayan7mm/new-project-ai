<?php

namespace App\Http\Requests\Admin;

use App\Enums\CategoryType;
use App\Support\CategoryIcons;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'type' => ['required', Rule::enum(CategoryType::class)],
            'is_digital' => ['required', 'boolean'],
            'icon' => ['required', Rule::in(CategoryIcons::keys())],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:1000000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'نام',
            'type' => 'نوع',
            'is_digital' => 'دیجیتال',
            'icon' => 'آیکون',
            'sort_order' => 'ترتیب نمایش',
        ];
    }
}
