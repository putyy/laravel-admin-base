<?php
declare(strict_types=1);

namespace Pt\LaravelAdminBase\Actions;

use Encore\Admin\Actions\RowAction;

class JumpAction extends RowAction
{
    public $name = '';
    public $href = '';

    /**
     * JumpActions constructor.
     * @param string $href
     * @param string $name
     */
    public function __construct(string $href, string $name = '跳转')
    {
        parent::__construct();
        $this->href = $href;
        $this->name = $name;
    }

    public function href(): string
    {
        return $this->href;
    }
}
