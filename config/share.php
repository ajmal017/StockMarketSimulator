<?
return [
	/**
	'GlobalTimezone' => [
		"US" => -12, //美国
		"CA" => -12, //加拿大
		"UK" => -7, //英国
		"DE" => -6, //德国
		"FR" => -6, //法国
		"IT" => -6, //意大利
		"PT" => -7, //葡萄牙
		"ES" => -6, //西班牙
		"CH" => -6, //瑞士
		"AU" => 2, //澳大利亚
		"NZ" => 5, //新西兰
		"CN" => 0, //北京时间
		"JP" => 1, //东京时间
	],
	**/
	'RankUpdatedEvery' => 600, //分钟计算

	'MarketLocationMap' =>[
		'sh' => 'china',
		'sz' => 'china',
		'ny' => 'us',
		'nas' => 'us',
		'us' => 'us',
		'hk' => 'hk',
	],

	'GlobalTimezone' => [
		"US" => 'America/New_York', //美国
		"CA" => 'America/Toronto', //加拿大
		"UK" => 'Europe/London', //英国
		"DE" => 'Europe/Berlin', //德国
		"FR" => 'Europe/Paris', //法国
		"IT" => 'Europe/Rome', //意大利
		"PT" => 'Europe/Lisbon', //葡萄牙
		"ES" => 'Europe/Madrid', //西班牙
		"CH" => 'Europe/Zurich', //瑞士
		"AU" => 'Australia/Sydney', //澳大利亚
		"NZ" => 'Pacific/Auckland', //新西兰
		"CN" => 'Asia/Shanghai', //北京时间
		"JP" => 'Asia/Tokyo', //东京时间
	],

	'MarketTimezone' => [
		'sh' => 'CN',
		'sz' => 'CN',
		'ny' => 'US',
		'nas' => 'US',
		'us' => 'US',
		'hk' => 'CN',
	],

	'MarketOpenTime' => [
		'sh' => '9:30-11:30,13:00-15:00',
		'sz' => '9:30-11:30,13:00-15:00',
		'ny' => '9:30-16:30',
		'nas' => '9:30-16:30',
		'us' => '9:30-16:30',
		'hk' => '10:00-12:30,14:30-16:00',
	],

	'fullcode_prefix' => [
		'sh' => 'sh',
		'sz' => 'sz',
		'ny' => 'gb_',
		'nas' => 'gb_',
		'us' => 'gb_',
		'hk' => 'hk',
	],

	'market_platform' => [
		'sina' => ['sh', 'sz', 'ny', 'nas', 'us', 'hk'],
	],

	'market_platform_price_query' =>[
		'sina' => 'http://hq.sinajs.cn/list=',
	],

	'unit_in_word' => [
		'sh' => '手',
		'sz' => '手',
	],

	'volume_unit' => [
		'sh' => 100,
		'sz' => 100,
	],

	'show_share_chart' => [
		'sh', 'sz', 'hk', 'us', 'nas', 'ny'
	],

	'share_chart_platform' =>[
		'sina' => ['sh', 'sz', 'hk', 'us', 'nas', 'ny'],

	],

	'has_bar_chart' => [
		'sh', 'sz', 'hk'
	],

	'share_chart_url_head' => [
		'sh' => 'http://image.sinajs.cn/newchart/',
		'sz' => 'http://image.sinajs.cn/newchart/',
		'hk' => 'http://image.sinajs.cn/newchart/hk_stock/',
		'us' => 'http://image.sinajs.cn/newchart/usstock/',
		'nas' => 'http://image.sinajs.cn/newchart/usstock/',
		'ny' => 'http://image.sinajs.cn/newchart/usstock/',
	],

	//T+D延迟:单位小时
	'trading_with_delay' => [
		'sh' => 24,
		'sz' => 24,
	],

	
];

