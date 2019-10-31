<?php
/*
|--------------------------------------------------------------------------
| 本地化 trait
|--------------------------------------------------------------------------
|
| 本trait 只适用于一个实体模型，只有一个字段需要本地化，例如，侧边栏，入口
| 实体模型包含 字段：locale，类型：json，值：{"en-US": "", "zh-CN": ""}
|
| 如果一个实体有很多字段需要实例化，例如产品的标题，简介，描述
| 参考使用：https://github.com/Astrotomic/laravel-translatable
*/

namespace Modules\Base\Support\Locale;

trait LocaleTrait
{
    public function getLocale($locale = null)
    {
        $locale = $locale ?: app()->getLocale();

        return $this->locale[$locale];
    }
}
