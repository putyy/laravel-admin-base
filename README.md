laravel-admin 基础类封装
======

## 安装

```shell
composer require putyy/laravel-admin-base
```

## 使用

### Controller

> 1. LaravelAdminBaseController 继承即可使用
> 2. 继承BaseController 根据项目实现对应方法
> 
> 重点: 由于laravel-admin对display的特别处理，需要在对应的model实现 \Pt\LaravelAdminBase\ShowColumnInterface 接口才能调用formatColumn
> 
> 示例(更多用法看源码):
```php
protected function grid()
{
    ...
    $grid->column('is_lock', __('Is lock'))->display($this->formatColumn('l'));
    $grid->column('img_url', __('Img url'))->display($this->formatColumn('a'));
    $this->formatTime($grid, [
        'create_time'=>'创建时间',
        'update_time'=>'更新时间',
    ]);
    ...
}
```

### Actions

> DeleteAction、LockAction 结合自身项目自定义action继承 实现对应的操作
>
> 如下：
>

```php
<?php
declare(strict_types=1);

namespace App\Admin\Actions;

class DeleteAction extends \Pt\AdminBase\Actions\DeleteAction
{
    /**
     * 假删除 需要设置的值
     * @return array
     */
    public function deleted(): array
    {
        return ['is_del' => 2];
    }
}
```

> 然后控制器调用

```php
<?php
declare(strict_types=1);

namespace App\Admin\Controllers;

class TestController extends \Pt\AdminBase\Http\Controllers\LaravelAdminBaseController
{
    // ...
    protected function grid()
    {
        // ...
        $grid->actions(function ($actions) {
            $actions->add(new \App\Admin\Actions\DeleteAction(false));
        });
        // ...
    }
    // ...
}
```

### 权限展示重写

![img.png](img.png)

> 1. admin_permissions表添加slug_group字段
     >
     >   ALTER TABLE `admin_permissions`
     > ADD COLUMN `slug_group` varchar(100) NOT NULL AFTER `slug`;
>
> 2. 添加路由
>

```php
<?php
Route::group([
    'prefix'        => config('admin.route.prefix'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {
    $router->prefix('auth-rewrite')->group(function (Router $router) {
        $router->resource('role', \Pt\AdminBase\Http\Controllers\RoleController::class);
        $router->get('role-permissions-detail/{id}', '\Pt\AdminBase\Http\Controllers\RoleController@detailPermission');
        $router->post('role-permissions-save', '\Pt\AdminBase\Http\Controllers\RoleController@savePermission');
        $router->resource('permissions', \Pt\AdminBase\Http\Controllers\PermissionsController::class);
    });
});
```

