<?php
/*******************************************************************************
 * rodzeta.redirect - SEO redirects module
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

namespace Rodzeta\Redirect\Options;

//use const Rodzeta\Redirect\CONFIG;

function Update($data) {
	\Encoding\PhpArray\Write(\Rodzeta\Redirect\CONFIG . "options.php", array(
		"redirect_www" => $data["redirect_www"],
		"redirect_https" => $data["redirect_https"],
		"redirect_slash" => $data["redirect_slash"],
		"redirect_index" => $data["redirect_index"],
		"redirect_multislash" => $data["redirect_multislash"],
		"redirect_urls" => $data["redirect_urls"],
	));
}

function Select() {
	$fname = \Rodzeta\Redirect\CONFIG . "options.php";
	var_dump
	$result = is_readable($fname)? include $fname : array(
		"redirect_www" => "Y",
		"redirect_https" => "",
		"redirect_slash" => "Y",
		"redirect_index" => "Y",
		"redirect_urls" => "Y",
	);
	return $result;
}
