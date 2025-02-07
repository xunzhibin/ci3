<?php

namespace Xzb\Ci3\Core\Controller;

use Xzb\Ci3\Core\Request;

trait Show
{
	/**
	 * 更新
	 * 
	 * @param \Xzb\Ci3\Core\Request
	 * @return \Xzb\Ci3\Core\Eloquent\Model
	 */
	public function show_get(Request $request)
	{
		extract($request->param());

		return $this->model::soleByPrimaryKey($id, $filter);
	}
}
