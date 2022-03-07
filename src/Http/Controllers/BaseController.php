<?php
declare(strict_types=1);

namespace Pt\LaravelAdminBase\Http\Controllers;

use Encore\Admin\Grid;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Encore\Admin\Controllers\AdminController;

abstract class BaseController extends AdminController
{
    /**
     * @var string
     */
    protected static $funcCallErrMsg = '';

    /**
     * @var string
     */
    protected static $funcCallErrCode = '';

    /**
     * @var string
     */
    protected static $funcCallErrString = '';

    /**
     * 返回图片URL
     * @param string $url
     * @return string
     */
    abstract function getImgUrl(string $url): string;

    /**
     * 返回视频URL
     * @param string $url
     * @return string
     */
    abstract function getVideoUrl(string $url): string;

    /**
     * 是否删除
     * @param int $lock
     * @return bool
     */
    abstract function isLock($lock): bool;

    /**
     * 可以更具环境返回错误信息
     * @return string
     */
    abstract static function getFailMsg(): string;

    /**
     * 响应成功消息
     * @param array|null $data
     * @param string $msg
     * @return JsonResponse
     */
    public static function success(array $data = null, string $msg = 'ok'): JsonResponse
    {
        return response()->json([
            'code' => 0,
            'msg' => $msg,
            'data' => $data
        ]);
    }

    /**
     * 响应失败消息
     * @param string $msg
     * @param array|null $data
     * @return JsonResponse
     */
    public static function fail(string $msg, array $data = null): JsonResponse
    {
        return response()->json([
            'code' => 1,
            'msg' => $msg,
            'data' => $data
        ]);
    }

    public function responseAction(?array $display = [], ?string $message = null, bool $status = true): JsonResponse
    {
        return response()->json([
            'display' => $display,
            'message' => $message,
            'status' => $status,
        ]);
    }

    /**
     * 事务执行闭包
     * @param callable $callback
     * @return false
     */
    public static function funCallback(callable $callback)
    {
        DB::beginTransaction();
        try {
            $res = $callback();
            Db::commit();
        } catch (\Throwable $e) {
            static::$funcCallErrMsg = $e->getMessage();
            static::$funcCallErrCode = $e->getCode();
            static::$funcCallErrString = $e->getFile() . '-' . $e->getLine() . '-' . $e->getMessage();
            $res = false;
            Db::rollback();
        }
        return $res;
    }

    protected static function ajaxCallbackNoTransaction(callable $callback): JsonResponse
    {
        try {
            $callback();
        } catch (\Throwable $e) {
            return static::fail(static::getFailMsg());
        }
        return static::success();
    }

    protected static function ajaxCallbackTransaction(callable $callback): JsonResponse
    {
        $res = static::funCallback($callback);
        if ($res === false) {
            return static::fail(static::getFailMsg());
        }
        return static::success();
    }

    /**
     * 格式化输出字段信息
     * @param string $type
     * @param string $other
     * @return \Closure
     */
    protected function formatColumn(string $type, string $other = ''): \Closure
    {
        switch ($type) {
            case 'i':
                return function ($value) {
                    return "<img src='{$this->getImgUrl($value)}' style='height: 100px;'>";
                };
            case 'v':
                return function ($video) use ($other) {
                    $other && $cover_img = $this->getImgUrl($other);
                    $video = $this->getVideoUrl($video);
                    return "<video controls style='height: 120px;' " . ($other ? "poster='{$cover_img}'" : "") . "><source src='{$video}' type='video/mp4'></video>";
                };
            case 'l':
                return function ($value) {
                    return $this->isLock($value) ? '正常' : '<span style="color: red;">锁定</span>';
                };
            case 't':
                return function ($value) {
                    return date('Y-m-d H:i:s', $value);
                };
            case 'm':
                return function ($value) {
                    return bcmul((string)$value, '0.01', 2);
                };
            default:
                return function ($value) {
                    return '无';
                };
        }
    }

    /**
     * 自定义页面分页
     * @param \Illuminate\Contracts\Pagination\LengthAwarePaginator $perPages
     * @param array $all
     * @param array|int[] $num_arr
     * @return string
     */
    public static function getPage($perPages, array $all = [], array $num_arr = [10, 20, 30, 50, 100]): string
    {
        foreach ($all as $k => $v) {
            if ($k == 'page') {
                unset($all[$k]);
            }
        }
        $html = '<div class="box-footer clearfix"> 从 <b>' . $perPages->firstItem() . '</b> 到 <b>' . $perPages->lastItem() . '</b> ，总共 <b>' . $perPages->total() . '</b> 条';

        $html .= str_replace(['<nav>', '<ul class="pagination">'], ['', '<ul class="pagination pagination-sm no-margin pull-right">'], $perPages->appends($all)->links());

        $html .= '<label class="control-label pull-right" style="margin-right: 10px; font-weight: 100;"> <small>显示</small>&nbsp; <select class="input-sm grid-per-pager" name="per-page">';

        foreach ($num_arr as $num) {
            $href = $perPages->url($perPages->currentPage()) . '&per_page=' . $num;
            if ($perPages->perPage() == $num) {
                $html .= '<option value=' . $href . ' selected>' . $num . '</option>';
            } else {
                $html .= '<option value=' . $href . '>' . $num . '</option>';
            }

        }

        $html .= '</select>&nbsp;<small>条</small></label></div>';

        return $html;
    }

    protected function disableAttribute(Grid $grid): Grid
    {
        //禁用导出按钮
        $grid->disableExport();
        //禁用创建按钮
        $grid->disableCreateButton();
        //禁用批量删除
        $grid->disableBatchActions();
        return $grid;
    }

    protected function disableAction(?callable $callback = null): \Closure
    {
        return function ($actions) use ($callback) {
            // 去掉查看
            $actions->disableView();
            $actions->disableEdit();
            $actions->disableDelete();
            $callback && $callback($actions);
        };
    }
}
