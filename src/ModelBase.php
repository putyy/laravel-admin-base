<?php
declare(strict_types=1);

namespace Pt\LaravelAdminBase;

use Encore\Admin\Extension;

class ModelBase implements ShowColumnInterface
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
     * 返回音频URL
     * @param string $url
     * @return string
     */
    public function getAudioUrl(string $url): string
    {
        return $url;
    }

    /**
     * 返回文件URL
     * @param string $url
     * @return string
     */
    public function getFileUrl(string $url): string
    {
        return $url;
    }

    /**
     * 是否锁定
     * @param int $lock
     * @return bool
     */
    public function isLock(int $lock): bool
    {
        return $lock === 2;
    }
}
