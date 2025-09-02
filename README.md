# ci3
CodeIgniter 3 扩展包

## 表单验证
	
|	验证规则		|	参数格式		|		说明		|		示例		|
|-------------------|-------------------|-------------------|-------------------|
| if_exist 			| 					| 验证数据中字段存在时验证 | if_exist		|
| bail 				| 					| 首次验证失败后终止其他规则验证 | bail		|
| required			| 					| 必填字段验证				| required		|
| type				| type:格式类型		| 验证字段类型 				| type:int		|
| length_comparison | length_comparison:比较符,比较值 | 长度比较验证 | length_comparison:>=,10 |
| size_comparison	| size_comparison:比较符,比较值	| 值大小比较验证 | size_comparison:<,100	|
| date_format		| date_format:日期时间格式 | 验证日期格式		| date_format:Y-m-d |
| date_comparison 	| date_comparison:比较符,比较值 | 日期比较验证 | date_comparison:>,2023-01-01 |
| regex 			| regex:表达式		| 正则表达式验证 			| regex:/^[a-z]+$/	|
| email 			| 					| 邮箱格式验证 				| email 			|
| url				| url:协议 			| URL格式验证				| url:https 		|
| same 				| same:对比字段 	| 两个字段值相等			| same:password_confirm |
| db_exists 		| db_exists:连接组.数据表,表列名,附加筛选列:值,.....| DB中存在(查询条数) | db_exists:users.name,id:$id|
| db_info			| 同 db_exists | DB中存在(查询信息) | db_exists:users.name,id:$id|


### type 规则 格式类型
	
|	说明			|	类型		|
|-------------------|-------------------|
| 整形 				| integer、int、long |
| 字符串			| string			|
| 布尔 				|  bool、boolean	|
| 数组 				|  array			|
| 自然数(非负数整形) |  natural			|
| 正整数(自然数无零) |  natural_no_zero |
| 数字 				|  numeric			|
| 字母 				|  alpha			|
| 字母数字			|  alpha_num、alpha_numeric |
| 字母数字、破折号、下划线 |  alpha_dash |

### length_comparison 规则 比较符
	
|	说明			|	符号		|
|-------------------|-------------------|
| 等于 				| =			|
| 小于等于 			| <=			|
| 大于等于 			| >=			|


### size_comparison 规则 比较符
	
|	说明			|	符号		|
|-------------------|-------------------|
| 小于 				| <			|
| 小于等于 			| <=			|
| 大于 				| >			|
| 大于等于 			| >=			|


### date_comparison 规则 比较符
	
|	说明			|	符号		|
|-------------------|-------------------|
| 小于 				| <			|
| 小于等于 			| <=			|
| 大于 				| >			|
| 大于等于 			| >=			|

### db_exists 规则 参数
	
|	参数			|	说明		|
|-------------------|-------------------|
| 连接组			| 非必填, 未填写使用默认 |
| 数据表 			| 必填					|
| 表列名 			| 非必填, 验证数据对应的数据表列名, 未填写使用验证数据键名. 使用附加筛选列时, 必填|
| 附加筛选列 		| 查询时增加额外查询条件, 对应查询表列名 |
| 附加筛选列值		| 自定义值 或者 某个验证数据(例如 id:$uid 列名为 id, 值是 验证数据中键名为uid的值)  |
