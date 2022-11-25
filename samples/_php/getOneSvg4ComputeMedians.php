<?php
header("Content-Type: text/plain");

include_once "encoding.php";
include_once "unicode.php";

$input=json_decode(file_get_contents('php://input'),true);
if(isset($input["c"])&&isset($input["lang"]))
{
	$c=$input["c"];
	$lang=$input["lang"];
}
else echo "Error: parameters missing!";

function errorMsgWhenPrintOneChar($msg)
{
	$s="";
	$s=$msg;
	return $s;
}

function printOneChar($c,$lang)
{
	$dec=decUnicode($c);
	$decZ=$dec."z";
	if ($lang=="Ja")
	{	// Japanese (ja)
		if (file_exists("../../svgsJa/".$dec.".svg")) $f="../../svgsJa/".$dec.".svg";
		else $f=null;
	}
	else if ($lang=="ZhHant")
	{	// traditional Chinese (zh-Hant)
		if (file_exists("../../svgsZhHant/".$dec.".svg")) $f="../../svgsZhHant/".$dec.".svg";
		else $f=null;
	}
	else
	{	// assume simplified Chinese (mainland China)
		if (file_exists("../../svgsZhHans/".$dec.".svg")) $f="../../svgsZhHans/".$dec.".svg";
		else $f=null;
	}
	if ($f) $s=file_get_contents($f);
	else $s=errorMsgWhenPrintOneChar("Error: cannot get ".$c);
	print $s;
}
printOneChar($c,$lang);
?>