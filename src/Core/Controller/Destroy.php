<?php

namespace Xzb\Ci3\Core\Controller;

use Xzb\Ci3\Core\Request;

trait Destroy
{
	/**
	 * 删除
	 * 
	 * @param \Xzb\Ci3\Core\Request
	 * @return array
	 */
	public function destroy_delete(Request $request)
	{
		extract($request->param());

		$this->model::deleteByPrimaryKey($id, $filter);

		return compact('id');
	}

}
