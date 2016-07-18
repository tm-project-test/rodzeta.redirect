<?php
/***********************************************************************************************
 * rodzeta.redirect - SEO redirects module
 * Copyright 2016 Semenov Roman
 * MIT License
 ************************************************************************************************/

namespace Rodzeta\Redirect;

final class Utils {

	const MAP_NAME = "/upload/cache.rodzeta.redirects.php";
	const SRC_NAME = "/upload/rodzeta.redirects.csv";

	static function createCache() {
		$basePath = $_SERVER["DOCUMENT_ROOT"];
		if (!file_exists($basePath . self::SRC_NAME)) {
			return;
		}
		$fcsv = fopen($basePath . self::SRC_NAME, "r");
		if ($fcsv === FALSE) {
			return;
		}

		$redirects = array();
		$i = 0;
		while (($row = fgetcsv($fcsv, 4000, "\t")) !== FALSE) {
			$i++;
			if ($i == 1) {
				continue;
			}
			if (count($row) != 2) {
				continue;
			}
			$redirects[trim($row[0])] = trim($row[1]);
		}
		fclose($fcsv);

		file_put_contents(
			$basePath . self::MAP_NAME,
			"<?php\nreturn " . var_export($redirects, true) . ";"
		);
	}

	static function clearMap() {
		if (file_exists($_SERVER["DOCUMENT_ROOT"] . self::MAP_NAME)) {
			unlink($_SERVER["DOCUMENT_ROOT"] . self::MAP_NAME);
		}
	}

	static function getMap() {
		$fname = $_SERVER["DOCUMENT_ROOT"] . self::MAP_NAME;
		if (!file_exists($fname) || !is_readable($fname)) {
			return array();
		}
		return include $fname;
	}

}
