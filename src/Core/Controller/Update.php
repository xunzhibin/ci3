<?php

namespace Xzb\Ci3\Core\Controller;

use Xzb\Ci3\Core\Request;

trait Update
{
	/**
	 * 更新
	 * 
	 * @param \Xzb\Ci3\Core\Request
	 * @return \Xzb\Ci3\Core\Eloquent\Model
	 */
	public function update_put(Request $request)
	{
		extract($request->param());

		return $this->model::updateByPrimaryKey($id, $data, $filter);
	}
}
