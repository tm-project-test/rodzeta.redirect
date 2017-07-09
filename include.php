<?php
/*******************************************************************************
 * rodzeta.redirect - SEO redirects module
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

namespace Rodzeta\Redirect;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

require __DIR__ . "/.init.php";

function HandlerRedirectUrl() {
	//if ($_SERVER["REQUEST_METHOD"] != "GET" && $_SERVER["REQUEST_METHOD"] != "HEAD") {
	//	return;
	//}
	// ignore scripts from /bitrix/, cli scripts and cron scripts
	if (php_sapi_name() == "cli"
			|| defined("BX_CRONTAB")
			|| \CSite::InDir("/bitrix/")) {
		return;
	}
	$currentOptions = Options();
	$host = $_SERVER["SERVER_NAME"];
	$protocol = !empty($_SERVER["HTTPS"])
		&& $_SERVER["HTTPS"] != "off"? "https" : "http";
	$port = !empty($_SERVER["SERVER_PORT"])
		&& $_SERVER["SERVER_PORT"] != "80"
		&& $_SERVER["SERVER_PORT"] != "443"?
			(":" . $_SERVER["SERVER_PORT"]) : "";
	$currentUri = $currentOptions["ignore_query"] == "Y"?
		parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH)
		: $_SERVER["REQUEST_URI"];
	$url = null;
	$isAbsoluteUrl = false;

	if ($currentOptions["redirect_www"] == "Y"
			&& substr($_SERVER["SERVER_NAME"], 0, 4) == "www.") {
		$host = substr($_SERVER["SERVER_NAME"], 4);
		$url = $currentUri;
	}

	$toProtocol = $currentOptions["redirect_https"];
	if ($toProtocol == "to_https" && $protocol == "http") {
		$protocol = "https";
		$url = $currentUri;
	} else if ($toProtocol == "to_http" && $protocol == "https") {
		$protocol = "http";
		$url = $currentUri;
	}

	if ($currentOptions["redirect_index"] == "Y"
			|| $currentOptions["redirect_slash"] == "Y"
			|| $currentOptions["redirect_multislash"] == "Y") {
		$changed = false;
		$u = parse_url($currentUri);
		if ($currentOptions["redirect_index"] == "Y") {
			$tmp = rtrim($u["path"], "/");
			if (basename($tmp) == "index.php") {
				$dname = dirname($tmp);
				$u["path"] = ($dname != DIRECTORY_SEPARATOR? $dname : "") . "/";
				$changed = true;
			}
		}
		if ($currentOptions["redirect_slash"] == "Y") {
			$tmp = basename(rtrim($u["path"], "/"));
			// add slash to url
			if (substr($u["path"], -1, 1) != "/"
					&& substr($tmp, -4) != ".php"
					&& substr($tmp, -4) != ".htm"
					&& substr($tmp, -5) != ".html") {
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
		if (isset($redirects[$currentUri])) {
			list($url, $status) = $redirects[$currentUri];
			if (substr($url, 0, 4) == "http") {
				$isAbsoluteUrl = true;
			}
		} else {
			// find part url
			foreach ($redirects as $fromUri => $v) {
				list($toUri, $status, $partUrl) = $v;
				if ($partUrl != "Y") {
					continue;
				}
				if (substr($currentUri, 0, strlen($fromUri)) == $fromUri) {
					$url = $toUri . substr($currentUri, strlen($fromUri));
					break;
				}
			}
		}
	}
	$status = $status == "302"?
		"302 Found" : "301 Moved Permanently";

	// host redirects
	$domainRedirects = Domains();
	if (!empty($domainRedirects[$host])) {
		$host = $domainRedirects[$host];
		if (empty($url)) {
			$url = $currentUri;
		}
	}

	if (!empty($url)) {
		if ($isAbsoluteUrl) {
			LocalRedirect($url, true, $status);
		} else {
			LocalRedirect($protocol . "://" . $host . $port . $url, true, $status);
		}
		exit;
	}
}

function init() {
	/*
	AddEventHandler("main", "OnPanelCreate", function () {
		global $USER, $APPLICATION;
		// TODO use rights "can edit content"
		if (!$USER->IsAdmin()) {
		  return;
		}

		$link = "javascript:" . $APPLICATION->GetPopupLink(array(
			"URL" => URL_ADMIN . ".php",
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
			"ICON" => "bx-panel-site-structure-icon",
			//"SRC" => URL_ADMIN . "/icon.gif",
			"TEXT" => Loc::getMessage("RODZETA_REDIRECT_BTN_OPTIONS"),
			"ALT" => Loc::getMessage("RODZETA_REDIRECT_BTN_OPTIONS"),
			"MAIN_SORT" => 2000,
			"SORT"      => 200
		));

		$link = "javascript:" . $APPLICATION->GetPopupLink(array(
			"URL" => URL_ADMIN . ".urls.php",
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
			"ICON" => "bx-panel-site-structure-icon",
			//"SRC" => URL_ADMIN . "/icon.gif",
			"TEXT" => Loc::getMessage("RODZETA_REDIRECT_BTN_REDIRECTS"),
			"ALT" => Loc::getMessage("RODZETA_REDIRECT_BTN_REDIRECTS"),
			"MAIN_SORT" => 2000,
			"SORT"      => 220
		));
	});
	*/

	AddEventHandler(
		"main",
		"OnBeforeProlog",
		__NAMESPACE__ . "\\HandlerRedirectUrl"
	);
}

init();
