<?php

namespace Xzb\Ci3\Core\Controller;

use Xzb\Ci3\Core\Request;

trait Filter
{
	/**
	 * 筛选列表
	 * 
	 * @param \Xzb\Ci3\Core\Request
	 * @return \Xzb\Ci3\Core\Eloquent\Collection
	 */
	public function filter_get(Request $request)
	{
		extract($request->param());
	
		return $this->model::where($filter)
							->likeGroup($likeColumns, $keyword)
							->orderBy($sort)
							->limit($perPage)
							->get();
	}

}
