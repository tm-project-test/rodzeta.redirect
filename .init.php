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

define(__NAMESPACE__ . "\CONFIG",
	$_SERVER["DOCUMENT_ROOT"] . "/local/config/." . ID . "/");

const FILE_OPTIONS = CONFIG . "options.php";
const FILE_REDIRECTS = CONFIG . "urls.csv";
const FILE_REDIRECTS_CACHE = CONFIG . "urls.php";
const FILE_REDIRECTS_DOMAINS = CONFIG . "domains.php";

require LIB . "encoding/include.php";

function AppendValues($data, $n, $v) {
	yield from $data;
	for ($i = 0; $i < $n; $i++) {
		yield  $v;
	}
}

function Options() {
	return is_readable(FILE_OPTIONS)?
		include FILE_OPTIONS : [
			"redirect_www" => "Y",
			"redirect_https" => "",
			"redirect_slash" => "Y",
			"redirect_index" => "Y",
			"use_redirect_urls" => "N",
			"ignore_query" => "Y",
		];
}

function OptionsUpdate($data) {
	if (!is_dir(CONFIG)) {
		@mkdir(CONFIG, 0777, true);
	}
	\Encoding\PhpArray\Write(FILE_OPTIONS, [
		"redirect_www" => $data["redirect_www"],
		"redirect_https" => $data["redirect_https"],
		"redirect_slash" => $data["redirect_slash"],
		"redirect_index" => $data["redirect_index"],
		"redirect_multislash" => $data["redirect_multislash"],
		"use_redirect_urls" => $data["use_redirect_urls"],
		"ignore_query" => $data["ignore_query"],
	]);
}

function Select($fromCsv = false) {
	if ($fromCsv) {
		return \Encoding\Csv\Read(FILE_REDIRECTS);
	}
	return is_readable(FILE_REDIRECTS_CACHE)?
		include FILE_REDIRECTS_CACHE : [];
}

function Update($data) {
	$urls = [];
	$urlsMap = [];
	foreach ($data["redirect_urls"] as $url) {
		$from = trim($url[0]);
		$to = trim($url[1]);
		if ($from != "" && $to != "") {
			$urls[] = $url;
			$urlsMap[$from] = [$to, trim($url[2]), trim($url[3])];
		}
	}
	if (!is_dir(CONFIG)) {
		@mkdir(CONFIG, 0777, true);
	}
	\Encoding\Csv\Write(FILE_REDIRECTS, $urls);
	\Encoding\PhpArray\Write(FILE_REDIRECTS_CACHE, $urlsMap);
}

function Domains() {
	return is_readable(FILE_REDIRECTS_DOMAINS)?
		include FILE_REDIRECTS_DOMAINS : [];
}
