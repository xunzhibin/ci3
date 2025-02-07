<?php

namespace Xzb\Ci3\Hooks;

use \LogicException;
use \Throwable;

/**
 * 语种代码标准
 *
 * ISO 639 语种代码，两个字母表示一个语种，比如：zh表示中文、en表示英文
 *
 * ISO 3166 国家地区代码，比如：CN是CHina简称、US是United States of America简称
 *
 * RFC 1766 一个组合方案，把 语种代码 和 国家地区代码 拼接，表示不同的国家地区使用的语种
 * 			比如：zh-CN表示中国大陆的中文、zh-TW表示台湾地区的中文、zh-HK表示香港地区的中文
 *
 * RFC4646 另一种组合方案，语种代码、子语种 和 国家地区代码 拼接，不过一般不用第三部分
 * 			比如：zh-Hans表示简体中文、zh-Hans-HK表示香港简体中文
 *
 *
 * 按国家划分可以使用 ISO 3166
 * 按语种划分可以使用 ISO 639
 * 具体到简体和繁体，RFC 1766 和 RFC4646 两个都可以
 * 只是想表示语种而不想纠结地区 RFC4646 会更加合适
 */
class Language
{
	/**
	 * 默认 语言
	 * 
	 * @var array
	 */
	protected $language = [ 'en_title' => 'English', 'locale_title' => 'English', 'abbr' => 'en', 'ci_package' => 'english' ];

	/**
	 * 解析 请求 语言
	 * 
	 * @return void
	 */
	public function boot()
	{
		try {
			// $languages = $this->getHeaderAcceptLanguages();
			$languages = $this->getLanguages($this->getHeaderAcceptLanguages());
		}
		catch (Throwable $e) {
			log_message('error', 'hooks_parse_language_error: ' . $e->getMessage() . ' ' . __FILE__ . ' ' . __LINE__);
			$languages = [];
		}

		$language = $languages ? reset($languages) : $this->language;

		load_class('Config', 'core')->set_item('language', $language['ci_package']);
		load_class('Config', 'core')->set_item('language_abbr', $language['abbr']);
	}

	/**
	 * 获取 请求头中 Accept-Language 集合
	 *
	 * @return array
	 */
	public function getHeaderAcceptLanguages(): array
	{
		$headerAcceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? null;
		if (! $headerAcceptLanguage) {
			throw new LogicException('Accept-Language does not exist');
		}

		// 解析 需要安装国际化扩展(intl)
		$language = locale_accept_from_http($headerAcceptLanguage);
		if (! $language) {
			throw new LogicException('Accept-Language cannot be parsed');
		}

		return array_unique([
			$language,
			locale_get_primary_language($language)
		]);
	}

	/**
	 * 获取 语言 信息
	 * 
	 * @param array $languages
	 * @return array
	 */
	public function getLanguages(array $languages): array
	{
		$supportedLanguages = array_column($this->supportedLanguages(), null, 'abbr');

		$languages = array_fill_keys($languages, null);

		return array_filter(
			array_merge($languages, array_intersect_key($supportedLanguages, $languages))
		);
	}

	/**
	 * 支持的语言
	 * 
	 * @return array
	 */
	public function supportedLanguages()
	{
		return config_item('supported_languages') ?? [];
		// return [
		// 	[ 'en_title' => 'Chinese', 'locale_title' => '中文', 'abbr' => 'zh', 'ci_package' => 'simplified-chinese' ],
		// 	[ 'en_title' => 'English', 'locale_title' => 'English', 'abbr' => 'en', 'ci_package' => 'english' ],
		// ];
	}

}
