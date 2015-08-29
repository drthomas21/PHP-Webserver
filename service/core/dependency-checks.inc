<?php
//Check class dependencies
$arrDependencies = array(
		"Thread"
);
$arrMissing = array();
foreach($arrDependencies as $classname) {
	if(!class_exists($classname)) {
		$arrMissing[] = $classname;
	}
}

if(!empty($arrMissing)) {
	exit("Cannot run: Missing ".(count($arrMissing) > 1 ? "classes":"class")." [".join(', ',$arrMissing)."]\r\n");
}

//Check function dependencies
$arrDependencies = array(
		"json_encode",
		"json_decode",
		"file_get_contents",
		"file_put_contents",
		"preg_replace",
		"preg_match"
);
$arrMissing = array();
foreach($arrDependencies as $func) {
	if(!function_exists($func)) {
		$arrMissing[] = $func;
	}
}

if(!empty($arrMissing)) {
	exit("Cannot run: Missing ".(count($arrMissing) > 1 ? "functions":"function")." [".join(', ',$arrMissing)."]\r\n");
}

unset($arrDependencies);
unset($arrMissing);