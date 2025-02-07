<?php

namespace Xzb\Ci3\Core\Eloquent\Traits;

trait Events
{
	/**
	 * 观察者 类
	 * 
	 * @var string
	 */
	protected $observer;

	/**
	 * 执行 模型 事件
	 * 
	 * @param string $event
	 * @return void
	 */
	protected function fireModelEvent($event)
	{
		if ($this->observer && method_exists($this->observer, $event)) {
			call_user_func([new $this->observer, $event], $this);
		}
	}

	//     /**
    //  * Fire the given event for the model.
    //  *
    //  * @param  string  $event
    //  * @param  bool  $halt
    //  * @return mixed
    //  */
    // protected function fireModelEvent($event, $halt = true)
    // {
    //     if (! isset(static::$dispatcher)) {
    //         return true;
    //     }

    //     // First, we will get the proper method to call on the event dispatcher, and then we
    //     // will attempt to fire a custom, object based event for the given event. If that
    //     // returns a result we can return that result, or we'll call the string events.
    //     $method = $halt ? 'until' : 'dispatch';

    //     $result = $this->filterModelEventResults(
    //         $this->fireCustomModelEvent($event, $method)
    //     );

    //     if ($result === false) {
    //         return false;
    //     }

    //     return ! empty($result) ? $result : static::$dispatcher->{$method}(
    //         "eloquent.{$event}: ".static::class, $this
    //     );
    // }
}
