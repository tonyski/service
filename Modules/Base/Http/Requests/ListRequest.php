<?php

namespace Modules\Base\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class ListRequest extends FormRequest
{
    /**
     * 列表请求类，用于获取列表数据的请求
     * 支持的请求参数，/ ?page=1&limit=20&filter[name]=admin&sort[name]=desc
     * page  : 当前页码
     * limit ：每页显示多少条
     * filter：列表过滤  name为要过滤的字段名
     * sort  ：列表排序  name为要排序的字段名
     */

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'page' => 'sometimes|integer|min:1',
            'limit' => 'sometimes|integer|min:1',
            'filter' => 'sometimes|array',
            'sort' => 'sometimes|array',
        ];
    }

    /**
     * 允许过滤的字段
     * @return array
     */
    abstract public function allowFilter(): array;

    /**
     * 允许排序的字段
     * @return array
     */
    abstract public function allowSort(): array;

}
