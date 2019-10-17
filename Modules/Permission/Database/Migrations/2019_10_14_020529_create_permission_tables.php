<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermissionTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');

        Schema::create($tableNames['permissions'], function (Blueprint $table) {
            $table->char('uuid', 32)->primary();
            $table->string('name')->comment('权限名，唯一')->unique();
            $table->string('guard_name');
            $table->unsignedTinyInteger('type')->comment('权限的类型，1，功能权限，2，入口权限，3首页入口权限')->default(1);
            $table->json('locale')->nullable()->default(null)->comment('本地化翻译样例，{"en-US":"Adding user privileges","zh-CN":"添加用户权限"}');
            $table->string('comment')->comment('备注信息')->default('');
            $table->timestamps();
        });

        Schema::create($tableNames['roles'], function (Blueprint $table) {
            $table->char('uuid', 32)->primary();
            $table->string('name')->comment('角色名，唯一')->unique();
            $table->string('guard_name');
            $table->unsignedTinyInteger('is_system')->default(0)->comment('是否系统创建的角色，0：不是，1：是');
            $table->json('locale')->nullable()->default(null)->comment('国际化样例，{"en-US":"superAdmin","zh-CN":"超管"}');
            $table->timestamps();
        });

        //此表为 用户拥有的零时权限
        Schema::create($tableNames['model_has_permissions'], function (Blueprint $table) use ($tableNames, $columnNames) {
            $table->char('permission_uuid', 32);

            $table->string('model_type');
            $table->char($columnNames['model_morph_key'],32);
            $table->index([$columnNames['model_morph_key'], 'model_type', ], 'model_has_permissions_model_id_model_type_index');

            $table->timestamp('expires_at')->comment('零时权限的过期时间');
            $table->timestamp('created_at')->nullable()->useCurrent();

            $table->foreign('permission_uuid')
                ->references('uuid')
                ->on($tableNames['permissions'])
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->primary(['permission_uuid', $columnNames['model_morph_key'], 'model_type'],
                    'model_has_permissions_permission_model_type_primary');
        });

        Schema::create($tableNames['model_has_roles'], function (Blueprint $table) use ($tableNames, $columnNames) {
            $table->char('role_uuid', 32);

            $table->string('model_type');
            $table->char($columnNames['model_morph_key'],32);
            $table->index([$columnNames['model_morph_key'], 'model_type', ], 'model_has_roles_model_id_model_type_index');

            $table->unsignedTinyInteger('is_default')->comment('是否是用户默认的角色，0：否，1：是')->default(0);
            $table->timestamp('created_at')->nullable()->useCurrent();

            $table->foreign('role_uuid')
                ->references('uuid')
                ->on($tableNames['roles'])
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->primary(['role_uuid', $columnNames['model_morph_key'], 'model_type'],
                    'model_has_roles_role_model_type_primary');
        });

        Schema::create($tableNames['role_has_permissions'], function (Blueprint $table) use ($tableNames) {
            $table->char('permission_uuid', 32);
            $table->char('role_uuid', 32);

            $table->timestamp('created_at')->nullable()->useCurrent();

            $table->foreign('permission_uuid')
                ->references('uuid')
                ->on($tableNames['permissions'])
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('role_uuid')
                ->references('uuid')
                ->on($tableNames['roles'])
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->primary(['permission_uuid', 'role_uuid'], 'role_has_permissions_permission_id_role_id_primary');
        });

        app('cache')
            ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tableNames = config('permission.table_names');

        Schema::drop($tableNames['role_has_permissions']);
        Schema::drop($tableNames['model_has_roles']);
        Schema::drop($tableNames['model_has_permissions']);
        Schema::drop($tableNames['roles']);
        Schema::drop($tableNames['permissions']);
    }
}
