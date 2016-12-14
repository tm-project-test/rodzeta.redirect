<?php
/*******************************************************************************
 * rodzeta.redirect - SEO redirects module
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

namespace Rodzeta\Redirect;

const ID = "rodzeta.redirect";
const APP = __DIR__ . "/";
const LIB = APP  . "lib/";
const URL_ADMIN = "/bitrix/admin/" . ID . "/";

define(__NAMESPACE__ . "\CONFIG",
	$_SERVER["DOCUMENT_ROOT"] . "/upload/" . $_SERVER["SERVER_NAME"] . "/." . ID . "/");
define(__NAMESPACE__ . "\FILE_REDIRECTS", CONFIG . ".urls.csv");
define(__NAMESPACE__ . "\FILE_REDIRECTS_CACHE", CONFIG . ".urls.php");

require LIB . "encoding/php-array.php";
require LIB . "encoding/csv.php";
require LIB . "options.php";

function StorageInit() {
	if (!is_dir(CONFIG)) {
		mkdir(CONFIG, 0700, true);
	}
}

function AppendValues($data, $n, $v) {
	yield from $data;
	for ($i = 0; $i < $n; $i++) {
		yield  $v;
	}
}

function Select($fromCsv = false) {
	$result = [];
	if ($fromCsv) {
		$result = \Encoding\Csv\Read(FILE_REDIRECTS);
	} else {
		$result = \Encoding\PhpArray\Read(FILE_REDIRECTS_CACHE);
	}
	return $result;
}

function Update($data) {
	$urls = [];
	foreach ($data["redirect_urls"] as $url) {
		if (trim($url[0]) != "" && trim($url[1]) != "") {
			$urls[] = $url;
		}
	}
	\Encoding\Csv\Write(FILE_REDIRECTS, $urls);
	\Encoding\PhpArray\Write(FILE_REDIRECTS_CACHE, $urls);
}
