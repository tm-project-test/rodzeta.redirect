<?php
/*******************************************************************************
 * rodzeta.redirect - SEO redirects module
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

namespace Rodzeta\Redirect;

defined("B_PROLOG_INCLUDED") and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Loader;
use Bitrix\Main\EventManager;

require __DIR__ . "/.init.php";

EventManager::getInstance()->addEventHandler("main", "OnPanelCreate", function () {
	global $USER, $APPLICATION;
	// TODO заменить на определение доступа к редактированию конента
	if (!$USER->IsAdmin()) {
	  return;
	}

	$link = "javascript:" . $APPLICATION->GetPopupLink(array(
		"URL" => URL_ADMIN,
		"PARAMS" => array(
			"resizable" => true,
			//"width" => 780,
			//"height" => 570,
			//"min_width" => 400,
			//"min_height" => 200,
			"buttons" => "[BX.CDialog.prototype.btnClose]"
		)
	));
  $APPLICATION->AddPanelButton(array(
		"HREF" => $link,
		"ICON"  => "bx-panel-site-structure-icon",
		//"SRC" => URL_ADMIN . "/icon.gif",
		"TEXT"  => "Типовые редиректы",
		"ALT" => "Типовые редиректы",
		"MAIN_SORT" => 2000,
		"SORT"      => 200
	));

	$link = "javascript:" . $APPLICATION->GetPopupLink(array(
		"URL" => URL_ADMIN . "/urls/",
		"PARAMS" => array(
			"resizable" => true,
			//"width" => 780,
			//"height" => 570,
			//"min_width" => 400,
			//"min_height" => 200,
			"buttons" => "[BX.CDialog.prototype.btnClose]"
		)
	));
  $APPLICATION->AddPanelButton(array(
		"HREF" => $link,
		"ICON"  => "bx-panel-site-structure-icon",
		//"SRC" => URL_ADMIN . "/icon.gif",
		"TEXT"  => "Список редиректов",
		"ALT" => "Список редиректов",
		"MAIN_SORT" => 2000,
		"SORT"      => 220
	));
});

EventManager::getInstance()->addEventHandler("main", "OnBeforeProlog", function () {
	if (($_SERVER["REQUEST_METHOD"] != "GET" && $_SERVER["REQUEST_METHOD"] != "HEAD")
			|| \CSite::InDir("/bitrix/")) {
		return;
	}
	$currentOptions = Options\Select();

	$host = $_SERVER["SERVER_NAME"];
	$protocol = !empty($_SERVER["HTTPS"])
		&& $_SERVER["HTTPS"] != "off"? "https" : "http";
	$port = !empty($_SERVER["SERVER_PORT"])
		&& $_SERVER["SERVER_PORT"] != "80"
		&& $_SERVER["SERVER_PORT"] != "443"?
			(":" . $_SERVER["SERVER_PORT"]) : "";
	$url = null;
	$isAbsoluteUrl = false;

	if ($currentOptions["redirect_www"] == "Y" && substr($_SERVER["SERVER_NAME"], 0, 4) == "www.") {
		$host = substr($_SERVER["SERVER_NAME"], 4);
		$url = $_SERVER["REQUEST_URI"];
	}

	$toProtocol = $currentOptions["redirect_https"];
	if ($toProtocol == "to_https" && $protocol == "http") {
		$protocol = "https";
		$url = $_SERVER["REQUEST_URI"];
	} else if ($toProtocol == "to_http" && $protocol == "https") {
		$protocol = "http";
		$url = $_SERVER["REQUEST_URI"];
	}

	if ($currentOptions["redirect_index"] == "Y"
			|| $currentOptions["redirect_slash"] == "Y"
			|| $currentOptions["redirect_multislash"] == "Y") {
		$changed = false;
		$u = parse_url($_SERVER["REQUEST_URI"]);
		if ($currentOptions["redirect_index"] == "Y") {
			$tmp = rtrim($u["path"], "/");
			if (basename($tmp) == "index.php") {
				$dname = dirname($tmp);
				$u["path"] = ($dname != DIRECTORY_SEPARATOR? $dname : "") . "/";
				$changed = true;
			}
		}
		if ($currentOptions["redirect_slash"] == "Y") {
			// add slash to url
			if (substr($u["path"], -1, 1) != "/"
					&& substr(basename(rtrim($u["path"], "/")), -4) != ".php") {
				$u["path"] .= "/";
				$changed = true;
			}
		}
		if ($currentOptions["redirect_multislash"] == "Y") {
			if (strpos($u["path"], "//") !== false) {
				$u["path"] = preg_replace('{/+}s', "/", $u["path"]);
				$changed = true;
			}
		}
		if ($changed) {
			$url = $u["path"];
			if (!empty($u["query"])) {
				$url .= "?" . $u["query"];
			}
		}
	}

	$status = "";
	if ($currentOptions["redirect_urls"] == "Y") {
		$redirects = Select();
		if (isset($redirects[$_SERVER["REQUEST_URI"]])) {
			list($url, $status) = $redirects[$_SERVER["REQUEST_URI"]];
			if (substr($url, 0, 4) == "http") {
				$isAbsoluteUrl = true;
			}
		}
	}
	$status = $status == "302"?
		"302 Found" : "301 Moved Permanently";

	if (!empty($url)) {
		if ($isAbsoluteUrl) {
			LocalRedirect($url, true, $status);
		} else {
			LocalRedirect($protocol . "://" . $host . $port . $url, true, $status);
		}
		exit;
	}
});
