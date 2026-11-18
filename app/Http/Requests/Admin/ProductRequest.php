<?php

namespace App\Http\Requests\Admin;

use App\Enums\ProductStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->isAdmin();
    }

    protected function prepareForValidation(): void
    {
        // تبدیل ارقام فارسی/عربی و حذف جداکننده‌ها در فیلدهای قیمت
        $prices = [];
        foreach (['price_factory_cash', 'price_factory_installment', 'price_baloochi_cash', 'price_baloochi_installment', 'sort_order'] as $field) {
            $value = (string) $this->input($field, '');
            $value = strtr($value, array_combine(
                [...mb_str_split('۰۱۲۳۴۵۶۷۸۹'), ...mb_str_split('٠١٢٣٤٥٦٧٨٩')],
                [...str_split('0123456789'), ...str_split('0123456789')],
            ));
            $value = str_replace([',', '٬', '،', ' '], '', $value);
            $prices[$field] = $value === '' ? 0 : $value;
        }

        $this->merge($prices);
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id;
        $price = ['required', 'integer', 'min:0', 'max:999999999999'];
        $url = ['nullable', 'string', 'max:2048', 'url:http,https'];

        return [
            'code' => ['required', 'string', 'max:50', Rule::unique('products', 'code')->ignore($productId)],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:500'],
            'brand' => ['required', 'string', 'max:100'],
            'origin' => ['nullable', 'string', 'max:100'],
            'status' => ['required', Rule::enum(ProductStatus::class)],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:1000000'],

            'price_factory_cash' => $price,
            'price_factory_installment' => $price,
            'factory_installment_terms' => ['nullable', 'string', 'max:255'],
            'price_baloochi_cash' => $price,
            'price_baloochi_installment' => $price,
            'baloochi_installment_terms' => ['nullable', 'string', 'max:255'],

            'image' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:max_width=4000,max_height=4000'],
            'image_url' => $url,
            'remove_image' => ['nullable', 'boolean'],
            'pdf' => ['nullable', 'file', 'mimes:pdf', 'mimetypes:application/pdf', 'max:10240'],
            'pdf_url' => $url,
            'remove_pdf' => ['nullable', 'boolean'],

            'extra_info' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'code' => 'کد',
            'category_id' => 'دسته',
            'name' => 'نام',
            'description' => 'توضیح',
            'brand' => 'برند',
            'origin' => 'کشور',
            'status' => 'وضعیت',
            'sort_order' => 'ترتیب نمایش',
            'price_factory_cash' => 'قیمت نقد کارخانه',
            'price_factory_installment' => 'قیمت اقساطی کارخانه',
            'factory_installment_terms' => 'شرایط اقساط کارخانه',
            'price_baloochi_cash' => 'قیمت نقد بلوچی',
            'price_baloochi_installment' => 'قیمت اقساطی بلوچی',
            'baloochi_installment_terms' => 'شرایط اقساط بلوچی',
            'image' => 'عکس',
            'image_url' => 'لینک عکس',
            'pdf' => 'فایل PDF',
            'pdf_url' => 'لینک PDF',
            'extra_info' => 'توضیحات',
        ];
    }
}
