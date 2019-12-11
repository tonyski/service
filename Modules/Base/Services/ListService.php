<?php
/**
 * Created by PhpStorm.
 * User: fly.fei
 * Date: 2019/11/27
 * Time: 14:56
 */

namespace Modules\Base\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Base\Http\Requests\ListRequest;
use Modules\Base\Contracts\ListServiceInterface;

class ListService implements ListServiceInterface
{
    /**
     * 传入一个模型和请求，返回模型分页列表
     * @param Model $model
     * @param ListRequest $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getList(Model $model, ListRequest $request): LengthAwarePaginator
    {
        $limit = $request->query('limit') ? (int)$request->query('limit') : null;
        $filter = $request->query('filter') ?: [];
        $sort = $request->query('sort') ?: [];
        $allowFilter = $request->allowFilter();
        $allowSort = $request->allowSort();

        $query = $model->query();
        if (sizeof($filter)) {
            foreach ($filter as $key => $value) {
                if (in_array($key, $allowFilter)) $query->where($key, $value);
            }
        }
        if (sizeof($sort)) {
            foreach ($sort as $key => $value) {
                if (in_array($key, $allowSort)) $query->orderBy($key, $value);
            }
        }

        return $query->paginate($limit);
    }
}
