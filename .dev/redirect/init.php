<?php

$customRedirects = include "config.php";

if (isset($customRedirects[$_SERVER["REQUEST_URI"]])) {
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: " . $customRedirects[$_SERVER["REQUEST_URI"]]);
	exit;
	//$_SERVER["REQUEST_URI"] = $_SERVER["REDIRECT_URL"] = $customRedirects[$_SERVER["REQUEST_URI"]];
}
unset($customRedirects);
