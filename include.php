<?php
/***********************************************************************************************
 * rodzeta.redirect - SEO redirects module
 * Copyright 2016 Semenov Roman
 * MIT License
 ************************************************************************************************/

defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Loader;
use Bitrix\Main\EventManager;
use Bitrix\Main\Config\Option;

EventManager::getInstance()->addEventHandler("main", "OnBeforeProlog", function () {
	if (CSite::InDir("/bitrix/")) {
		return;
	}

	$protocol = !empty($_SERVER["HTTPS"])? "https" : "http";
	$host = $_SERVER["SERVER_NAME"];
	$url = null;

	if (Option::get("rodzeta.redirect", "redirect_www") == "Y" && substr($_SERVER["SERVER_NAME"], 0, 4) == "www.") {
		$host = substr($_SERVER["SERVER_NAME"], 4);
		$url = $_SERVER["REQUEST_URI"];
	}
	if (Option::get("rodzeta.redirect", "redirect_https") == "Y" && $protocol == "http") {
		$protocol = "https";
		$url = $_SERVER["REQUEST_URI"];
	}
	$redirects = \Rodzeta\Redirect\Utils::getMap();
	if (isset($redirects[$_SERVER["REQUEST_URI"]])) {
		$url = $redirects[$_SERVER["REQUEST_URI"]];
	}

	if (!empty($url)) {
		LocalRedirect($protocol . "://" . $host . $url, true, "301 Moved Permanently");
		exit;
	}
});
