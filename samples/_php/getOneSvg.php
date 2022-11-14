<?php
header("Content-Type: text/plain");
$input=json_decode(file_get_contents('php://input'),true);
if(isset($input["c"])&&isset($input["lang"]))
{
	$c=$input["c"];
	$lang=$input["lang"];
}
else echo "Error: parameters missing!";

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

function printOneChar($c,$lang)
{
	$dec=decUnicode($c);
	if ($lang=="ja")
	{	// Japanese (ja)
		if (file_exists("../../svgsJa/".$dec.".svg")) $f="../../svgsJa/".$dec.".svg";
		else $f=null;
	}
	else
	{	// Chinese (mainland china)
		if (file_exists("../../svgsZhHans/".$dec.".svg")) $f="../../svgsZhHans/".$dec.".svg";
		else $f=null;
	}
	if ($f) echo file_get_contents($f);
	else echo "Error: no data!";
}
printOneChar($c,$lang);
?>