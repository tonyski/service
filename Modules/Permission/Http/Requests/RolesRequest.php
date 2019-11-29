<?php

namespace Modules\Permission\Http\Requests;

use Modules\Base\Http\Requests\ListRequest;

class RolesRequest extends ListRequest
{
    /**
     * Get the validation rules that apply to the request.
     * 支持的请求参数 ?page=1&limit=20&filter[guard_name]=admin&sort[name]=desc
     *
     * @return array
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                'filter.guard_name' => 'in:admin,customer,supplier',
                'sort.name' => 'in:asc,desc'
            ]
        );
    }

    public function allowFilter(): array
    {
        return ['guard_name','name'];
    }

    public function allowSort(): array
    {
        return ['name'];
    }
}
