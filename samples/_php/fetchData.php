<?php
header("Content-type:application/json;charset=utf-8");
include_once __DIR__."/encoding.php";
include_once __DIR__."/unicode.php";
include_once __DIR__."/convertKana.php";
$input=json_decode(file_get_contents('php://input'),true);
if (isset($input["data"])&&$input["data"]) $data=$input["data"];
else $data="";
if (isset($input["lang"])&&$input["lang"]) $lang=$input["lang"];
else $lang="ja";
if (isset($input["special"])&&$input["special"]) $special=($input["special"]==="1");
else $special=false;
function getSvgsFileName($dec,$dir,$lang,$special)
{
	$file=$dec.".svg";
	$f=null;
	if($lang=="ja")
	{ // Japanese (ja)
		if ($special&&file_exists($dir."svgsJaSpecial/".$file)) $f=$dir."svgsJaSpecial/".$file;
		else if (file_exists($dir."svgsJa/".$file)) $f=$dir."svgsJa/".$file;
		else if (file_exists($dir."svgsJaKana/".$file)) $f=$dir."svgsJaKana/".$file;
	}
	else if ($lang=="ko")
	{ // Korean (ko)
		if ($special&&file_exists($dir."svgsKoSpecial/".$file)) $f=$dir."svgsKoSpecial/".$file;
		else if (file_exists($dir."svgsKo/".$file)) $f=$dir."svgsKo/".$file;
		else if (file_exists($dir."svgsKoJamo/".$file)) $f=$dir."svgsKoJamo/".$file;
		else if (file_exists($dir."svgsKoHangul/".$file)) $f=$dir."svgsKoHangul/".$file;
	}
	else if ($lang=="zh-Hans")
	{ // simplified Chinese (zh-Hans)
		if ($special&&file_exists($dir."svgsZhHansSpecial/".$file)) $f=$dir."svgsZhHansSpecial/".$file;
		else if (file_exists($dir."svgsZhHans/".$file)) $f=$dir."svgsZhHans/".$file;
	}
	else if ($lang=="zh-Hant")
	{ // traditional Chinese (zh-Hant)
		if ($special&&file_exists($dir."svgsZhHantSpecial/".$file)) $f=$dir."svgsZhHantSpecial/".$file;
		else if (file_exists($dir."svgsZhHant/".$file)) $f=$dir."svgsZhHant/".$file;
	}
	return $f;
}
function makeQuestionableSvg($dec,$c)
{
	$s='<svg viewBox="0 0 1024 1024">';
	$s.='<text x="50%" y="900" text-anchor="middle" font-size="1024" font-family="sans-serif">'.$c.'</text>';
	$s.='</svg>';
	return $s;
}
function getDicoData($dec,$c)
{
	global $lang;
	$f=__DIR__."/../../dictionary".str_replace("-","",ucfirst($lang)).".txt";
	if(file_exists($f)) $s=file_get_contents($f);
	else $s="";
	if(preg_match("/\{\"character\":\"".$c."\"[^\}]+\}/",$s,$m)) $s=$m[0];
	else $s="{\"character\":\"".$c."\"}";
	$o=json_decode($s);
	if(property_exists($o,"on")) $o->{'on'}=convertJapaneseOn($o->{'on'});
	if(property_exists($o,"kun")) $o->{'kun'}=convertJapaneseKun($o->{'kun'});
	return $o;
}
$k=0;
$dir=__DIR__."/../../";
$r=array();
foreach($data as $dec)
{
	$k++;
	// get no more than 20 chars if online server
	if(($k>20)&&($_SERVER['SERVER_NAME']!="localhost")) break;
	$c=unichr($dec);
	$f=getSvgsFileName($dec,$dir,$lang,$special);
	if($f) $svg=file_get_contents($f);
	else $svg=makeQuestionableSvg($dec,$c);
	$o=getDicoData($dec,$c);
	$o->{'svg'}=$svg;
	$r[]=$o;
}
echo json_encode($r);
?>