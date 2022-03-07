<?php
declare(strict_types=1);

namespace Pt\LaravelAdminBase\Actions;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

abstract class DeleteAction extends RowAction
{
    use CommonTrait;

    public $name = '删除';

    protected $confirmMessage = '确定删除?';

    /**
     * 假删除 需要设置的值
     * @return array
     */
    abstract function deleted(): array;

    /**
     * DeleteActions constructor.
     * @param bool $isDel 1为真删除
     * @param array $callback
     */
    public function __construct(bool $isDel = false, array $callback = [])
    {
        parent::__construct();
        $this->attribute('data-_is_del', $isDel ? 1 : 2);
        $this->attribute('data-_callback', $callback ? implode('_', $callback) : '');
    }

    public function handle(Model $model, Request $request)
    {
        DB::beginTransaction();
        try {
            $isDel = (int)$request->post('_is_del');
            if ($isDel === 1) {
                // 真删除
                $model->delete();
            } else {
                foreach ($this->deleted() as $k => $v) {
                    $model->$k = $v;
                }
                $model->save();
            }
            $this->handleChild($model, $request);
            DB::commit();
            return $this->response()->success('操作成功')->refresh();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->response()->error('产生错误：' . $e->getMessage());
        }
    }
}
