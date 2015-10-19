<?php

$metadata = (object)[
	'keywords' => [
		'Laravel', 'Laravel 5', 'Laravel 5.1',
		'Framework', 'WebFramework',
		'Sample',
	],
	'description' => '',
	'author' => 'Fumio Furukawa',
	'url' => 'http://jumilla.me',
];



if (!function_exists('date_string')) {
	/**
	 * ロケール 'ja' の日付表現を返します。
	 * ex) 2015年1月1日(木)
	 *
	 * @param  \Carbon\Carbon $datetime
	 */
	function date_string($datetime) {
		$weekdaysJa = ['日', '月', '火', '水', '木', '金', '土'];
		return $datetime->format('Y年n月j日').'('.$weekdaysJa[$datetime->dayOfWeek].')';
	}
}
