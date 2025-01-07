<?php
include_once __DIR__."/encoding.php";
include_once __DIR__."/unicode.php";
include_once __DIR__."/convertKana.php";
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
$dico="";
$file="../../svgs".$lang."/".$dec.".svg";
if($lang&&$dec&&file_exists($file)) $svg=file_get_contents($file);
if($lang&&$dec&&($svg!=""))
{
	$line=file_get_contents("../../dictionary".$lang.".txt");
	if($line)
	{
		if(preg_match("/(\{\"character\":\"".$c."[^}]+\})/u",$line,$m))
		{
			$d=json_decode($m[1]);
			$dico="";
			if(property_exists($d,"radical")) $dico.="<div>Radical: ".$d->radical."</div>";
			if(property_exists($d,"decomposition")) $dico.="<div>Decomposition: ".$d->decomposition."</div>";
			if(property_exists($d,"definition")) $dico.="<div>Definition: ".$d->definition."</div>";
			if(($lang=="Ja")&&property_exists($d,"on"))
			{
				$dico.="<div>Onyomi: ";
				$first=true;
				foreach($d->on as $a)
				{
					if($first) $first=false;
					else $dico.=", ";
					$dico.=convertJapaneseOn($a);
				}
				$dico.="</div>";
			}
			if(($lang=="Ja")&&property_exists($d,"kun"))
			{
				$dico.="<div>Kunyomi: ";
				$first=true;
				foreach($d->kun as $a)
				{
					if($first) $first=false;
					else $dico.=", ";
					$dico.=convertJapaneseKun($a);
				}
				$dico.="</div>";
			}
			if((($lang=="ZhHans")||($lang=="ZhHant"))&&property_exists($d,"pinyin"))
			{
				$dico.="<div>Pinyin: ";
				$first=true;
				foreach($d->pinyin as $a)
				{
					if($first) $first=false;
					else $dico.=", ";
					$dico.=str_replace(" ",", ",preg_replace("/\([^)]+\)/u","",$a));
				}
				$dico.="</div>";
			}
		}
	}
}
echo json_encode(["svg"=>$svg,"dico"=>$dico]);
?>
