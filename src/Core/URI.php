<?php
namespace Xzb\Ci3\Core;

use Xzb\Ci3\Helpers\Str;

abstract class URI extends \CI_URI
{
// ---------------------------------- 重构 ----------------------------------
	/**
	 * 构造函数
	 * 
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->initConfig();
	}

	/**
	 * 初始化 相关 配置
	 * 
	 * @return void
	 */
	protected function initConfig()
	{
		// URI: 语言/系统/应用/客户端类型/版本/模块
		// 例如: zh/jinnangyun/backend/api/v1/sites
			// 语言: 中文(zh)、英文(en)
			// 系统: 锦囊云(jinnangyun)、锦囊专家(jnexpert)
			// 应用: 前台(frontend)、后台(backend)、内部(internal call)、个性化定制(prsonalzation)
			// 客户端类型: api、pc、mobile、app、web、wechat、mini_prograb

		// list($language, $system, $app, $clientType, $version, $module) = array_values($this->segments);
		list($system, $app, $clientType, $version, $module) = array_values($this->segments);
		$this->config->set_item('system', $system);
		$this->config->set_item('app', $app);
		$this->config->set_item('client_type', $clientType);
		$this->config->set_item('version', $version);
		$this->config->set_item('module', Str::singularize($module));
	}

}
