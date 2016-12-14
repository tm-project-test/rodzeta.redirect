<?php

namespace Encoding\PhpArray;

if (!function_exists("\Encoding\PhpArray\Write")) {

function Write($fname, $data) {
	file_put_contents(
		$fname,
		"<?php\nreturn " . var_export($data, true) . ";"
	);
}

}
