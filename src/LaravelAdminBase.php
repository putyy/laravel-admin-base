<?php

namespace Pt\LaravelAdminBase;

use Encore\Admin\Extension;

class LaravelAdminBase extends Extension
{
    public $name = 'admin-base';

    public $views = __DIR__.'/../resources/views';

    public $assets = __DIR__.'/../resources/assets';

    public $menu = [
        'title' => 'LaravelAdminBase',
        'path'  => 'admin-base',
        'icon'  => 'fa-gears',
    ];
}
