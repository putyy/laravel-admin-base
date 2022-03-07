<?php
declare(strict_types=1);

namespace Pt\LaravelAdminBase\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

trait CommonTrait
{
    protected $callback;

    public function dialog(): void
    {
        $this->confirmMessage && $this->confirm($this->confirmMessage);
    }

    public function handleChild(Model $model, Request $request): void
    {
        $callback = $request->post('_callback');
        if($callback){
            list($class,$func) = explode('_',$callback);
            if(class_exists($class) && method_exists($class, $func)){
                $class::$func($model, $request);
            }
        }
    }
}
