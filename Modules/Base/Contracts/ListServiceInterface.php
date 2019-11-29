<?php
/**
 * Created by PhpStorm.
 * User: fly.fei
 * Date: 2019/11/27
 * Time: 15:24
 */

namespace Modules\Base\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Base\Http\Requests\ListRequest;

interface ListServiceInterface
{
    /**
     * 列表服务,用于返回简单的模型的列表数据
     */

    /**
     * 传入一个模型和请求，根据请求参数，返回模型的分页列表
     *
     * @param Model $model
     * @param ListRequest $request
     * @return LengthAwarePaginator
     */
    public function getList(Model $model, ListRequest $request): LengthAwarePaginator;
}
