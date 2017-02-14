<?php
/*******************************************************************************
 * rodzeta.redirect - SEO redirects module
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

namespace Rodzeta\Redirect;

define(__NAMESPACE__ . "\ID", "rodzeta.redirect");
define(__NAMESPACE__ . "\APP", __DIR__ . "/");
define(__NAMESPACE__ . "\LIB", APP  . "lib/");
define(__NAMESPACE__ . "\URL_ADMIN", "/bitrix/admin/" . ID . "/");

define(__NAMESPACE__ . "\CONFIG",
	$_SERVER["DOCUMENT_ROOT"] . "/upload/"
	. (substr($_SERVER["SERVER_NAME"], 0, 4) == "www."?
			substr($_SERVER["SERVER_NAME"], 4) : $_SERVER["SERVER_NAME"])
	. "/." . ID . "/");
define(__NAMESPACE__ . "\FILE_REDIRECTS", CONFIG . ".urls.csv");
define(__NAMESPACE__ . "\FILE_REDIRECTS_CACHE", CONFIG . ".urls.php");
define(__NAMESPACE__ . "\FILE_REDIRECTS_DOMAINS", CONFIG . ".domains.php");

require LIB . "encoding/php-array.php";
require LIB . "encoding/csv.php";
require LIB . "options.php";

function StorageInit() {
	if (!is_dir(CONFIG)) {
		mkdir(CONFIG, 0700, true);
	}
}

function AppendValues($data, $n, $v) {
	//yield from $data;
	$result = array();
	foreach ($data as $v) {
		$result[] = $v;
	}
	for ($i = 0; $i < $n; $i++) {
		//yield  $v;
		$result[] = $v;
	}
	return $result;
}

function Select($fromCsv = false) {
	$result = array();
	if ($fromCsv) {
		$result = \Encoding\Csv\Read(FILE_REDIRECTS);
	} else {
		$result = is_readable(FILE_REDIRECTS_CACHE)?
			include FILE_REDIRECTS_CACHE : array();
	}
	return $result;
}

function Update($data) {
	$urls = array();
	$urlsMap = array();
	foreach ($data["redirect_urls"] as $url) {
		$from = trim($url[0]);
		$to = trim($url[1]);
		if ($from != "" && $to != "") {
			$urls[] = $url;
			$urlsMap[$from] = array($to, trim($url[2]));
		}
	}
	\Encoding\Csv\Write(FILE_REDIRECTS, $urls);
	\Encoding\PhpArray\Write(FILE_REDIRECTS_CACHE, $urlsMap);
}

function SelectDomains() {
	return is_readable(FILE_REDIRECTS_DOMAINS)?
		include FILE_REDIRECTS_DOMAINS
		: array();
}
