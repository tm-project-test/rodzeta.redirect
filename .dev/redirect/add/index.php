<?php

require $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";

$path = dirname(__DIR__);

$fcsv = fopen($path . "/redirects.csv", "r");
if ($fcsv === FALSE) {
	return;
}

$redirects = include $path . "/config.php";
if (empty($redirects)) {
	$redirects = [];
}

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
	$path . "/config.php",
	"<?php\nreturn " . var_export($redirects, true) . ";"
);

echo "OK";
