<?php
declare(strict_types=1);

namespace Pt\LaravelAdminBase\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Pt\LaravelAdminBase\Actions\JumpAction;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;

class RoleController extends LaravelAdminBaseController
{
    /**
     * {@inheritdoc}
     */
    protected function title()
    {
        return trans('admin.roles');
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $roleModel = config('admin.database.roles_model');

        $grid = new Grid(new $roleModel());

        $grid->column('id', 'ID')->sortable();
        $grid->column('slug', trans('admin.slug'));
        $grid->column('name', trans('admin.name'));
        $grid->column('created_at', trans('admin.created_at'));
        $grid->column('updated_at', trans('admin.updated_at'));
        $grid->disableFilter();
        $grid->tools(function (Grid\Tools $tools) {
            $tools->batch(function (Grid\Tools\BatchActions $actions) {
                $actions->disableDelete();
            });
        });
        $grid->actions(function (Grid\Displayers\Actions $actions) {
            $actions->disableView();
            if ($actions->row->slug == 'administrator') {
                $actions->disableDelete();
                $actions->disableEdit();
            }else{
                $actions->add(new JumpAction('/admin/auth-rewrite/role-permissions-detail/' . $actions->row->id, '权限'));
            }
        });
        return $grid;
    }


    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
        $roleModel = config('admin.database.roles_model');

        $form = new Form(new $roleModel());

        $form->display('id', 'ID');

        $form->text('slug', trans('admin.slug'))->rules('required');
        $form->text('name', trans('admin.name'))->rules('required');
        $form->display('created_at', trans('admin.created_at'));
        $form->display('updated_at', trans('admin.updated_at'));

        return $form;
    }


    public function detailPermission($id, Content $content): Content
    {
        /**
         * @var $permissionModel \Illuminate\Database\Query\Builder | \Illuminate\Database\Eloquent\Model
         */
        $permissionModel = config('admin.database.permissions_model');
        /**
         * @var $roleModel \Illuminate\Database\Query\Builder | \Illuminate\Database\Eloquent\Model
         */
        $roleModel = config('admin.database.roles_model');
        $permissionsAll = $permissionModel::orderBy('name')->select(['id', 'name', 'slug_group'])->get()->toArray();
        $data = $roleModel::with(['permissions' => function ($query) {
            $query->select('id');
        }])
            ->where('id', $id)
            ->first()
            ->toArray();
        return $content->title('权限')->view('admin-base::edit-permission', [
            'data' => $data,
            'permissions_all' => $this->arrayGroupBy($permissionsAll, 'slug_group'),
            'permissions_use' => $data['permissions'] ? array_column($data['permissions'], 'id') : [],
        ]);
    }

    public function savePermission(Request $request): JsonResponse
    {
        $role_id = $request->post('role_id');
        $permissions_ids = $request->post('permissions_ids');
        return parent::ajaxCallbackTransaction(function () use ($role_id, $permissions_ids) {
            $role_permissions_table = config('admin.database.role_permissions_table');
            DB::table($role_permissions_table)->where('role_id', $role_id)->delete();
            $data = [];
            $at = date('Y-m-d H:i:s');
            foreach (explode(',', $permissions_ids) as $v) {
                $data[] = [
                    'role_id' => $role_id,
                    'permission_id' => $v,
                    'created_at' => $at,
                    'updated_at' => $at,
                ];
            }
            DB::table($role_permissions_table)->insert($data);
        });
    }

    protected function arrayGroupBy(array $arr, string $key) : array
    {
        $grouped = array();
        foreach ($arr as $k => $value) {
            $grouped[$value[$key]][$k] = $value;
        }
        if (func_num_args() > 2) {
            $args = func_get_args();
            foreach ($grouped as $key => $value) {
                $params = array_merge(array($value), array_slice($args, 2, func_num_args()));
                $grouped[$key] = call_user_func_array('array_group_by', $params);
            }
        }
        return $grouped;
    }
}
