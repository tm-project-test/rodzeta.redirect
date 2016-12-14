<?php
/*******************************************************************************
 * rodzeta.redirect - SEO redirects module
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

namespace Rodzeta\Redirect\Options;

use Bitrix\Main\Config\Option;

use const Rodzeta\Redirect\CONFIG;

function Update($data) {
	\Encoding\PhpArray\Write(CONFIG . "options.php", [
		// TODO
		/*
		"fields" => $fields,
		"fields_bitrix24" => $fieldsBitrix24,
		"fields_csv" => $fieldsCsv,
		*/
	]);
}

function Select() {
	$fname = CONFIG . "options.php";
	$result = is_readable($fname)? include $fname : [
		// TODO
		/*
		"fields" => [],
		"fields_bitrix24" => [],
		"fields_csv" => [],
		*/
	];
	return $result;
}
