# ci3
CodeIgniter 3 扩展包

表单验证
	验证规则
	IfExist
		存在时验证: if_exist
		属性首次验证失败后立即终止其它规则验证: bail
		必填: required
		类型: type:格式类型
				格式类型: int、integer、string、bool、array、numeric、alpha、alpha_num、alpha_dash
					整形: integer、int、long
					字符串: string
					布尔: bool、boolean
					数组: array
					自然数:natural
					正整数(自然数无零): natural_no_zero
					数字: numeric
					字母: alpha
					字母数字: alpha_num、alpha_numeric
					字母数字、破折号、下划线: alpha_dash
		长度: length_comparison:比较符,比较值
				比较符: =、<=、>=
				比较值: 自然数
		值:   size_comparison:比较符,比较值
				比较符: <、<=、>、>=
				比较值: 自然数 或者 比较字段键名(验证数据中某个字段)
		日期: date_format:日期时间格式
				日期时间格式: PHP格式 https://www.php.net/manual/zh/datetime.format.php, 可以使用组合格式, 例如 Y-m-d H:i:s
			  date_comparison:比较符,比较值
				比较符: <、<=、>、>=
				比较值: 日期时间格式的值 或者 比较字段键名(验证数据中某个字段)
		正则: regex:表达式
				表达式: 符合PHP的正则表达式
		邮箱: email
		URL:  url:协议
				协议: http、https、...
		字段值相等:same:比较字段键名
				比较字段键名: 验证数据中某个字段
		DB存在: db_exists:数据库连接组.数据表,对应数据表列名,附加筛选列1:附加筛选列1值,附加筛选列2:附加筛选列2值,.....
					数据库连接组: 非必填, 未填写使用默认
					数据表: 必填
					对应数据表列名: 非必填, 未填写使用验证字段键名. 使用附加筛选列时, 必填
					附加筛选列值: 自定义输入值 或者 字段键名(验证数据中某个字段, 例如 title:$project_title 数据表列名:title,值: 验证数据中 键名为:project_title 的值)
				db_info:数据库连接组.数据表,对应数据表列名,附加筛选列1:附加筛选列1值,附加筛选列2:附加筛选列2值,.....
					相关参数 同 db_exists
