<?php
/**
 * Created by PhpStorm.
 * User: fly.fei
 * Date: 2019/11/27
 * Time: 15:24
 */

namespace Modules\Base\Contracts;

use Illuminate\Database\Eloquent\Model;
use Modules\Base\Http\Requests\ListRequest;

interface ListServiceInterface
{
    /**
     * 列表服务,用于返回简单的模型的列表数据
     */

    /**
     *  传入一个模型和请求，返回模型分页 或者 模型集合 或者一个空数组
     *
     * @param Model $model
     * @param ListRequest $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getList(Model $model, ListRequest $request);
}
