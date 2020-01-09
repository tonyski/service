<?php
/**
 * Created by PhpStorm.
 * User: fly.fei
 * Date: 2019/11/27
 * Time: 14:56
 */

namespace Modules\Base\Services;

use Illuminate\Database\Eloquent\Model;
use Modules\Base\Http\Requests\ListRequest;
use Modules\Base\Contracts\ListServiceInterface;

class ListService implements ListServiceInterface
{
    /**
     * 传入一个模型和请求，返回模型分页 或者 模型集合 或者一个空数组
     *
     * @param Model $model
     * @param ListRequest $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getList(Model $model, ListRequest $request)
    {
        $page = $request->query('page') ? (int)$request->query('page') : null;
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

        return ($page || $limit) ? $query->paginate($limit) : $query->get();
    }
}
