<?php
include_once "encoding.php";
include_once "unicode.php";
include_once "convertKana.php";
$input=json_decode(file_get_contents('php://input'),true);
if(isset($input["lang"])) $lang=$input["lang"];
else $lang="Ja";
$lang=preg_replace("/[^A-Za-z_-]/","",$lang);
if(isset($input["dec"])) $dec=$input["dec"];
else $dec="";
$dec=preg_replace("/[^0-9]/","",$dec);
if($dec) $c=unichr($dec);
else $c=null;
if($lang&&$dec) $svg=file_get_contents("../../svgs".$lang."/".$dec.".svg");
else $svg=null;
if($lang&&$dec)
{
	$f=file_get_contents("../../dictionary".$lang.".txt");
	if($f)
	{
		if(preg_match("/(\{\"character\":\"".$c."[^}]+\})/u",$f,$m))
		{
			$d=json_decode($m[1]);
			$dico="";
			if($d->radical) $dico.="<div>Radical: ".$d->radical."</div>";
			if($d->decomposition) $dico.="<div>Decomposition: ".$d->decomposition."</div>";
			if($d->definition) $dico.="<div>Definition: ".$d->definition."</div>";
			if(($lang=="Ja")&&$d->on)
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
			if(($lang=="Ja")&&$d->kun)
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
			if((($lang=="ZhHans")||($lang=="ZhHant"))&&$d->pinyin)
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
		else $dico=null;
	}
	else $dico=null;
}
else $dico=null;
echo json_encode(["svg"=>$svg,"dico"=>$dico]);
?>
