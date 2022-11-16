<?php
header("Content-Type: text/plain");

function decUnicode($u)
{
	$len=strlen($u);
	if ($len==0) return 63;
	$r1=ord($u[0]);
	if ($len==1) return $r1;
	$r2=ord($u[1]);
	if ($len==2) return (($r1&31)<< 6)+($r2&63);
	$r3=ord($u[2]);
	if ($len==3) return (($r1&15)<<12)+(($r2&63)<< 6)+($r3&63);
	$r4=ord($u[3]);
	if ($len==4) return (($r1& 7)<<18)+(($r2&63)<<12)+(($r3&63)<<6)+($r4&63);
	return 63;
}

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