<?php

Route::get('admin/fetchMenu', function () {
    $data = [
        'index' => [
            'uuid' => 'i-1',
            'name' => 'home',
            'route' => '/home',
            'locale' => '首页入口',
            'comment' => '备注',
        ],
        'menu' => [
            [
                'uuid' => '0',
                'parent_uuid' => '',
                'name' => 'system',
                'icon' => 'ios-navigate',
                'comment' => '备注',
                'locale' => '系统设置',
                'route' => [
                    [
                        'uuid' => '0-1',
                        'name' => 'permission',
                        'route' => '/permission',
                        'locale' => '权限管理',
                        'comment' => '备注',
                    ]
                ],
                'menu' => []
            ]
        ]
    ];

    $returnData = [
        'status'=>'success',
        'code'=>200,
        'message'=>'ok',
        'data'=>$data
    ];

    return $returnData;
});



