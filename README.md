# ci3
CodeIgniter 3 扩展包

## 表单验证

### 基础 规则
|	规则		|		说明						|
|-----------|-------------------------------|
| if_exist	| 验证数据中字段存在时验证			|
| bail		| 首次验证失败后终止其他规则验证	|
| required	| 必填字段验证					|


### 类型 规则
|	规则					|		说明								|		PHP 代码																				|
|-----------------------|---------------------------------------|-------------------------------------------------------------------------------------------|
| type:int				| 验证值 必须是 整形						| filter_var(验证值, FILTER_VALIDATE_INT)													|
| type:string			| 验证值 必须是 字符串					| is_string(验证值);																			|
| type:bool				| 验证值 必须是 布尔						| in_array(验证值, [true, false, 0, 1, '0', '1'], true);										|
| type:array			| 验证值 必须是 数组						| is_array(验证值)																			|
| type:numeric			| 验证值 必须是 数字						| is_numeric(验证值)																			|
| type:natural			| 验证值 必须是 自然数(非负数整形)			| filter_var(验证值, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0,] ])				|
| type:natural_no_zero	| 验证值 必须是 正整数(自然数无零)			| filter_var(验证值, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1,] ])				|
| type:alpha			| 验证值 必须是 字母						| is_string(验证值) && preg_match('/\A[a-zA-Z]+\z/u', 验证值)									|
| type:alpha_numeric	| 验证值 必须是 字母、数字					| (is_string(验证值) || is_numeric(验证值)) && preg_match('/\A[a-zA-Z0-9]+\z/u', 验证值)		|
| type:alpha_dash		| 验证值 必须是 字母、数字、破折号、下划线	| (is_string(验证值) || is_numeric(验证值)) && preg_match('/\A[a-zA-Z0-9_-]+\z/u', 验证值)	|


### 值比较 规则
|	规则					|		说明								|		PHP 代码						|
|-----------------------|---------------------------------------|-----------------------------------|
| size_in:指定值1,...	| 验证值 必须在 指定值 的列表中			| in_array(验证值, [指定值1,...])		|
| size_lt:指定值			| 验证值 必须 小于 指定值					| 验证值 < 指定值						|
| size_lte:指定值		| 验证值 必须 小于或等于 指定值			| 验证值 <= 指定值					|
| size_gt:指定值			| 验证值 必须 大于 指定值					| 验证值 > 指定值						|
| size_gte:指定值		| 验证值 必须 大于或等于 指定值			| 验证值 >= 指定值					|
| same:指定字段			| 验证值 必须与 指定字段值 匹配			| 验证值 === 指定字段值				|


### 长度 规则
|	规则				|			说明				|		PHP 代码			|
|-------------------|---------------------------|-----------------------|
| length_exact:32	| 必须 等于 指定长度			| 验证长度 == 指定长度	|
| length_max:20		| 必须 小于或等于 指定长度	| 验证长度 <= 指定长度	|
| length_min:4		| 必须 大于或等于 指定长度	| 验证长度 >= 指定长度	|


### 日期时间 规则
|	规则						|		说明									|	PHP 代码										|
|---------------------------|-------------------------------------------|-----------------------------------------------|
| date_format:Y-m-d			| 验证值 必须是 Y-m-d 格式					| DateTime::createFromFormat('!'.格式, 验证值)	|
| date_format:Y-m-d H:i:s	| 验证值 必须是 Y-m-d H:i:s 格式				| DateTime::createFromFormat('!'.格式, 验证值)	|
| date_format:U				| 验证值 必须是 Unix时间戳 格式				| DateTime::createFromFormat('!'.格式, 验证值)	|
| date_format:U,Y-m-d		| 验证值 必须是 Unix时间戳 或者 Y-m-d 格式		| DateTime::createFromFormat('!'.格式, 验证值)	|
| date_in:指定值1,...		| 验证值 必须在 指定值 的列表中				| in_array(验证值时间戳, [指定值时间戳1,...])		|
| date_lt:指定值				| 验证值 必须 小于 指定值						| 验证值时间戳 < 指定值时间戳						|
| date_lte:指定值			| 验证值 必须 小于或等于 指定值				| 验证值时间戳 <= 指定值时间戳						|
| date_gt:指定值				| 验证值 必须 大于 指定值						| 验证值时间戳 > 指定值时间戳						|
| date_gte:指定值			| 验证值 必须 大于或等于 指定值				| 验证值时间戳 >= 指定值时间戳						|


### 数据库 规则
|	规则																	|		说明					|		连接数据库							| 执行SQL:  																								|
|-----------------------------------------------------------------------|---------------------------|-------------------------------------------|-------------------------------------------------------------------------------------------------------|
| db_exists:指定表														| 必须 存在于 指定数据表中	| 使用 config/database.php 文件中 默认 数据库	| `SELECT COUNT(*) FROM 指定表 WHERE 验证字段键名 = 验证字段值`											|
| db_exists:指定表,指定列													| 必须 存在于 指定数据表中	| 使用 config/database.php 文件中 默认 数据库	| `SELECT COUNT(*) FROM 指定表 WHERE 指定列 = 验证字段值`													|
| db_exists:指定表,指定列,附加列:值										| 必须 存在于 指定数据表中	| 使用 config/database.php 文件中 默认 数据库	| `SELECT COUNT(*) FROM 指定表 WHERE 指定列 = 验证字段值 AND 附加列=值`									|
| db_exists:指定表,指定列,附加列1:值1,附加列2:$验证字段a键名				| 必须 存在于 指定数据表中	| 使用 config/database.php 文件中 默认 数据库	| `SELECT COUNT(*) FROM 指定表 WHERE 指定列 = 验证字段值 AND 附加列1 = 值1 AND 附加列2 = 验证字段a值`		|
| db_exists:指定连接组.指定表												| 必须 存在于 指定数据表中	| 使用 config/database.php 文件中 指定 数据库	| `SELECT COUNT(*) FROM 指定表 WHERE 验证字段键名 = 验证字段值`											|
| db_exists:指定连接组.指定表,指定列										| 必须 存在于 指定数据表中	| 使用 config/database.php 文件中 指定 数据库	| `SELECT COUNT(*) FROM 指定表 WHERE 指定列 = 验证字段值`													|
| db_exists:指定连接组.指定表,指定列,附加列:值								| 必须 存在于 指定数据表中	| 使用 config/database.php 文件中 指定 数据库	| `SELECT COUNT(*) FROM 指定表 WHERE 指定列 = 验证字段值 AND 附加列=值`									|
| db_exists:指定连接组.指定表,指定列,附加列1:值1,附加列2:$验证字段a键名,...	| 必须 存在于 指定数据表中	| 使用 config/database.php 文件中 指定 数据库	| `SELECT COUNT(*) FROM 指定表 WHERE 指定列 = 验证字段值 AND 附加列1 = 值1 AND 附加列2 = 验证字段a值 ...`	|
| db_info:指定表															| 必须 存在于 指定数据表中	| 使用 config/database.php 文件中 默认 数据库	| `SELECT * FROM 指定表 WHERE 验证字段键名 = 验证字段值`													|
| db_info:指定表,指定列													| 必须 存在于 指定数据表中	| 使用 config/database.php 文件中 默认 数据库	| `SELECT * FROM 指定表 WHERE 指定列 = 验证字段值`														|
| db_info:指定表,指定列,附加列:值											| 必须 存在于 指定数据表中	| 使用 config/database.php 文件中 默认 数据库	| `SELECT * FROM 指定表 WHERE 指定列 = 验证字段值 AND 附加列=值`											|
| db_info:指定表,指定列,附加列1:值1,附加列2:$验证字段a键名					| 必须 存在于 指定数据表中	| 使用 config/database.php 文件中 默认 数据库	| `SELECT * FROM 指定表 WHERE 指定列 = 验证字段值 AND 附加列1 = 值1 AND 附加列2 = 验证字段a值`				|
| db_info:指定连接组.指定表												| 必须 存在于 指定数据表中	| 使用 config/database.php 文件中 指定 数据库	| `SELECT * FROM 指定表 WHERE 验证字段键名 = 验证字段值`													|
| db_info:指定连接组.指定表,指定列										| 必须 存在于 指定数据表中	| 使用 config/database.php 文件中 指定 数据库	| `SELECT * FROM 指定表 WHERE 指定列 = 验证字段值`														|
| db_info:指定连接组.指定表,指定列,附加列:值								| 必须 存在于 指定数据表中	| 使用 config/database.php 文件中 指定 数据库	| `SELECT * FROM 指定表 WHERE 指定列 = 验证字段值 AND 附加列=值`											|
| db_info:指定连接组.指定表,指定列,附加列1:值1,附加列2:$验证字段a键名,...	| 必须 存在于 指定数据表中	| 使用 config/database.php 文件中 指定 数据库	| `SELECT * FROM 指定表 WHERE 指定列 = 验证字段值 AND 附加列1 = 值1 AND 附加列2 = 验证字段a值 ...`			|


### 电子邮箱 规则
|	规则			|		说明					|		PHP 代码																|
|---------------|---------------------------|---------------------------------------------------------------------------|
| email			| 验证值 必须是 电子邮件 格式	| filter_var((string)$value, FILTER_VALIDATE_EMAIL, $option = 0) 			|
| email:unicode	| 验证值 必须是 电子邮件 格式	| filter_var((string)$value, FILTER_VALIDATE_EMAIL, $option = 'unicode')	|


### 网址 规则
|	规则			|		说明					|		PHP 代码																																							|
|---------------|---------------------------|-----------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| url			| 验证值 必须是 有效的 URL	| filter_var((string)$value, FILTER_VALIDATE_URL)																														|
| url:协议		| 验证值 必须是 有效的 URL	| in_array(strtolower((string)parse_url((string)验证值, PHP_URL_SCHEME)), array_map('strtolower', [协议]), true) && filter_var((string)$value, FILTER_VALIDATE_URL)		|
| url:协议1,...	| 验证值 必须是 有效的 URL	| in_array(strtolower((string)parse_url((string)验证值, PHP_URL_SCHEME)), array_map('strtolower', [协议1,...]), true) && filter_var((string)$value, FILTER_VALIDATE_URL)	|


### 正则表达式 规则
|	规则				|		说明						|		PHP代码				|
|-------------------|-------------------------------|---------------------------|
| regex:/^[a-z]+$/	| 验证值 必须匹配 指定 正则表达式	| preg_match(表达式, 验证值)	|

