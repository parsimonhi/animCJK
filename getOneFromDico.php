<?php
include_once "lib.php";
$input=json_decode(file_get_contents('php://input'),true);
if(isset($input["data"])&&$input["data"])
{
	$char=$input["data"];
	if(isset($input["lang"])&&$input["lang"]) $lang=$input["lang"];
	else $lang="zh-hans";
	echo getDictionaryData($char,$lang)."\n";
}
else echo "Error, no data!"."\n";
?>