<?php

namespace Xzb\Ci3\Core\Controller;

use Xzb\Ci3\Core\Request;

trait OffsetPaginate
{
	/**
	 * 偏移量分页
	 * 
	 * @param \Xzb\Ci3\Core\Request
	 * @return \Xzb\Ci3\Core\Eloquent\Paginator
	 */
	public function index_get(Request $request)
	{
		extract($request->param());

		return $this->model::where($filter)
						->likeGroup($likeColumns, $keyword)
						->orderBy($sort)
						->offsetPaginate($perPage);
	}

}
