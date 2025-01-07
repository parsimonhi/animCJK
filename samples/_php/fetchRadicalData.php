<?php
include_once __DIR__."/encoding.php";
include_once __DIR__."/unicode.php";
$input=json_decode(file_get_contents('php://input'),true);
if(isset($input["lang"])) $lang=$input["lang"];
else $lang="Ja";
$lang=preg_replace("/[^A-Za-z_-]/","",$lang);
if(isset($input["dec"])) $dec=$input["dec"];
else $dec="";
$dec=preg_replace("/[^0-9]/","",$dec);
if($dec) $c=unichr($dec);
else $c=null;
$svg="";
$decomposition="";
if($lang&&$dec)
{
	// get svg file
	// some characters are special because some strokes are split to show the radical
	$special="../../svgs".$lang."Special/".$dec.".svg";
	if (file_exists($special)) $svg=file_get_contents($special);
	else
	{
		$file="../../svgs".$lang."/".$dec.".svg";
		if (file_exists($file)) $svg=file_get_contents($file);
	}
}
if($lang&&$dec&&($svg!=""))
{
	$line=file_get_contents("../../dictionary".$lang.".txt");
	if($line)
	{
		if(preg_match("/(\{\"character\":\"".$c."[^}]+\})/u",$line,$m))
		{
			$d=json_decode($m[1]);
			if(property_exists($d,"acjks")) $decomposition=$d->acjks;
			else if(property_exists($d,"acjk")) $decomposition=$d->acjk;
		}
	}
}
echo json_encode(["svg"=>$svg,"acjk"=>$decomposition]);
?>
