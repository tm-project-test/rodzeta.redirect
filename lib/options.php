<?php
/*******************************************************************************
 * rodzeta.redirect - SEO redirects module
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

namespace Rodzeta\Redirect\Options;

use const Rodzeta\Redirect\CONFIG;

function Update($data) {
	\Encoding\PhpArray\Write(CONFIG . "options.php", [
		"redirect_www" => $data["redirect_www"],
		"redirect_https" => $data["redirect_https"],
		"redirect_slash" => $data["redirect_slash"],
		"redirect_index" => $data["redirect_index"],
		"redirect_multislash" => $data["redirect_multislash"],
	]);
}

function Select() {
	$fname = CONFIG . "options.php";
	$result = is_readable($fname)? include $fname : [
		"redirect_www" => "Y",
		"redirect_https" => "",
		"redirect_slash" => "Y",
		"redirect_index" => "Y",
		"redirect_multislash" => "Y",
	];
	return $result;
}
