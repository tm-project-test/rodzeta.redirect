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

require LIB . "encoding/php-array.php";
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
