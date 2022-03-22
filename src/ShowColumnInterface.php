<?php
declare(strict_types=1);

namespace Pt\LaravelAdminBase;

use Encore\Admin\Extension;

interface ShowColumnInterface
{
    /**
     * 返回图片URL
     * @param string $url
     * @return string
     */
    function getImgUrl(string $url): string;

    /**
     * 返回视频URL
     * @param string $url
     * @return string
     */
    function getVideoUrl(string $url): string;

    /**
     * 返回音频URL
     * @param string $url
     * @return string
     */
    function getAudioUrl(string $url): string;

    /**
     * 返回文件URL
     * @param string $url
     * @return string
     */
    function getFileUrl(string $url): string;

    /**
     * 是否锁定
     * @param int $lock
     * @return bool
     */
    function isLock(int $lock): bool;
}
