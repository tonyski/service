<?php

namespace Modules\Route\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateMenuRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $locales = config('app.locales');
        $localeRule = [];
        foreach ($locales as $k => $v) {
            $localeRule['locale.' . $k] = 'required';
        }

        return array_merge([
            'locale' => 'required|array',
            'name' => 'bail|required|max:255|regex:/^[a-z]+(\.[a-z]+)*$/|unique:route_menus',// 格式为 aaa     aaa.bbb    aaa.bbb.ccc
            'guard_name' => 'required|in:admin,customer,supplier',
            'comment' => 'max:255',
        ], $localeRule);
    }
}
