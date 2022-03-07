<?php
declare(strict_types=1);

namespace Pt\LaravelAdminBase\Actions;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommonAction extends RowAction
{
    public $name = '';

    protected $confirmMessage = '是否确认？';

    use CommonTrait;

    /**
     * @param array $data
     * @param string $name
     * @param array $callback
     */
    public function __construct(array $data = [], string $name = '默认', array $callback = [])
    {
        parent::__construct();
        $this->name = $name;
        $this->attribute('data-_data', $data ? json_encode($data) : '');
        $this->attribute('data-_callback', $callback ? implode('_', $callback) : '');
    }

    /**
     * @param Model $model
     * @param Request $request
     * @return \Encore\Admin\Actions\Response
     */
    public function handle(Model $model, Request $request)
    {
        try {
            $data = $request->post('_data');
            $data = $data ? json_decode($data,true) : [];
            if (empty($data)) {
                return $this->response()->success('操作成功')->refresh();
            }
            foreach ($data as $k => $v) {
                $model->$k = $v;
            }
            DB::beginTransaction();
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
