<?php
header("Content-Type: text/plain");
include_once __DIR__."/encoding.php";
include_once __DIR__."/getCharList.php";
$input=json_decode(file_get_contents('php://input'),true);
if(isset($input["s"]))
{
	$set=preg_replace("/[^A-Za-z0-9_-]/","",$input["s"]);
	echo getCharList($set);
}
else echo "";

?>