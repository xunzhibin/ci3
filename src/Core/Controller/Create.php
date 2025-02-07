<?php

namespace Xzb\Ci3\Core\Controller;

use Xzb\Ci3\Core\Request;

trait Create
{
	/**
	 * 创建
	 * 
	 * @param \Xzb\Ci3\Core\Request
	 * @return \Xzb\Ci3\Core\Eloquent\Model
	 */
	public function store_post(Request $request)
	{
		return $this->model::create($request->param());
	}
}
