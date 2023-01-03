<?php
include_once "encoding.php";
include_once "unicode.php";
$input=json_decode(file_get_contents('php://input'),true);
if(isset($input["lang"])) $lang=$input["lang"];
else $lang="Ja";
$lang=preg_replace("/[^A-Za-z_-]/","",$lang);
if(isset($input["dec"])) $dec=$input["dec"];
else $dec="";
$dec=preg_replace("/[^0-9]/","",$dec);
if($dec) $c=unichr($dec);
else $c=null;
if($lang&&$dec)
{
	// get svg file
	// some characters are special because some strokes are split to show the radical
	$special="../../svgs".$lang."Special/".$dec.".svg";
	if (file_exists($special)) $svg=file_get_contents($special);
	else $svg=file_get_contents("../../svgs".$lang."/".$dec.".svg");
}
else $svg=null;
if($lang&&$dec)
{
	$f=file_get_contents("../../dictionary".$lang.".txt");
	if($f)
	{
		if(preg_match("/(\{\"character\":\"".$c."[^}]+\})/u",$f,$m))
		{
			$d=json_decode($m[1]);
			if(property_exists($d,"acjks")) $decomposition=$d->acjks;
			else if(property_exists($d,"acjk")) $decomposition=$d->acjk;
			else $decomposition=null;
		}
		else $decomposition=null;
	}
	else $decomposition=null;
}
else $decomposition=null;
echo json_encode(["svg"=>$svg,"acjk"=>$decomposition]);
?>
