<?php
/***********************************************************************************************
 * rodzeta.redirect - SEO redirects module
 * Copyright 2016 Semenov Roman
 * MIT License
 ************************************************************************************************/

return function () {
	$mapName = "/upload/cache.rodzeta.redirects.php";
	$fname = "/upload/rodzeta.redirects.csv";

	$basePath = $_SERVER["DOCUMENT_ROOT"];

	/*
	$path = $basePath . dirname($mapName);
	if (!is_dir($path)) {
		mkdir($path, 0777, true);
	}
	*/

	$fcsv = fopen($basePath . $fname, "r");
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
		$basePath . $mapName,
		"<?php\nreturn " . var_export($redirects, true) . ";"
	);

};