<?php
declare(strict_types=1);

namespace Pt\LaravelAdminBase\Http\Controllers;

class LaravelAdminBaseController extends BaseController
{
    /**
     * 返回图片URL
     * @param string $url
     * @return string
     */
    public function getImgUrl(string $url): string
    {
        return $url;
    }

    /**
     * 返回视频URL
     * @param string $url
     * @return string
     */
    public function getVideoUrl(string $url): string
    {
        return $url;
    }

    /**
     * 是否锁定
     * @param int $lock
     * @return bool
     */
    public function isLock($lock): bool
    {
        return $lock === 2;
    }

    /**
     * 可以更具环境返回错误信息
     * @return string
     */
    public static function getFailMsg(): string
    {
        return static::$funcCallErrString;
    }
}
