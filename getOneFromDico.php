<?php
header("Content-Type: text/plain");

include "lib.php";

if (isset($_POST["data"])&&$_POST["data"])
{
	if (isset($_POST["lang"])&&$_POST["lang"]) $lang=$_POST["lang"];
	else $lang="zh-Hans";
	$char=$_POST["data"];
	echo getDictionaryData($char,$lang)."\n";
}

?>