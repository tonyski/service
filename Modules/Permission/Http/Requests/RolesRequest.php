<?php

namespace Modules\Permission\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RolesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * 支持的请求参数 ?page=1&limit=20&filter[guard_name]=admin&sort[name]=desc
     *
     * @return array
     */
    public function rules()
    {
        return [
            'page' => 'numeric|min:1',
            'limit' => 'numeric|min:1',
            'filter' => 'array',
            'filter.guard_name' => 'in:admin,customer,supplier',
            'sort' => 'array',
            'sort.name' => 'in:asc,desc',
        ];
    }
}
