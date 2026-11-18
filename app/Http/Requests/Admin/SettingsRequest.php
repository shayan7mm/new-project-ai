<?php

namespace App\Http\Requests\Admin;

use App\Services\SettingService;
use Illuminate\Foundation\Http\FormRequest;

class SettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->isAdmin();
    }

    public function rules(): array
    {
        $rules = [];
        foreach (array_keys(SettingService::EDITABLE) as $key) {
            $rules[$key] = ['nullable', 'string', 'max:255'];
        }

        $rules['companyName'] = ['required', 'string', 'max:150'];
        $rules['email'] = ['nullable', 'email', 'max:255'];
        $rules['instagramUrl'] = ['nullable', 'url:http,https', 'max:2048'];
        $rules['logoUrl'] = ['nullable', 'url:http,https', 'max:2048'];
        $rules['address'] = ['nullable', 'string', 'max:500'];
        $rules['notice'] = ['nullable', 'string', 'max:2000'];
        $rules['logo'] = ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'];
        $rules['remove_logo'] = ['nullable', 'boolean'];

        return $rules;
    }

    public function attributes(): array
    {
        return SettingService::EDITABLE + ['logo' => 'فایل لوگو'];
    }
}
