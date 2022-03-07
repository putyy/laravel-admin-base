<?php
declare(strict_types=1);

namespace Pt\LaravelAdminBase\Actions;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

abstract class LockAction extends RowAction
{
    public $name = '锁定';

    protected $confirmMessage = '是否确认？';

    use CommonTrait;

    /**
     * 根据当前锁定状态返回需要修改的键值数组
     * @param Model $model
     * @return array
     */
    abstract function saved(Model $model): array;

    /**
     * @param string $name
     * @param array $callback
     */
    public function __construct(string $name = '锁定', array $callback = [])
    {
        parent::__construct();
        $this->attribute('data-_callback', $callback ? implode('_', $callback) : '');
        $this->name = $name;
    }

    /**
     * @param Model $model
     * @param Request $request
     * @return \Encore\Admin\Actions\Response
     */
    public function handle(Model $model, Request $request): \Encore\Admin\Actions\Response
    {
        DB::beginTransaction();
        try {
            foreach ($this->saved($model) as $k => $v) {
                $model->$k = $v;
            }
            $model->save();
            $this->handleChild($model, $request);
            DB::commit();
            return $this->response()->success('操作成功')->refresh();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->response()->error('产生错误：' . $e->getMessage());
        }
    }
}
