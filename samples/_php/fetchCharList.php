<?php
header("Content-type:application/json;charset=utf-8");
include_once __DIR__."/encoding.php";
include_once __DIR__."/getCharList.php";
$input=json_decode(file_get_contents('php://input'),true);
if(isset($input["s"])) $set=$input["s"];
else $set="Ja";
if(isset($input["map"])) $map=json_decode($input["map"],true);
else
{
	// minimal map
	$map=[];
	$map["Ja"]=[];
	$map["Ko"]=[];
	$map["ZhHans"]=[];
	$map["ZhHant"]=[];
}
function makeTitle($s)
{
	if($s=="hiragana") return "Hiragana";
	if($s=="katakana") return "Katakana";
	if($s=="g1") return "Grade 1";
	if($s=="g2") return "Grade 2";
	if($s=="g3") return "Grade 3";
	if($s=="g4") return "Grade 4";
	if($s=="g5") return "Grade 5";
	if($s=="g6") return "Grade 6";
	if($s=="g7") return "Junior high school";
	if($s=="g8") return "Jinmeiyō";
	if($s=="g9") return "Hyōgai";
	if($s=="gc") return "Components";
	if($s=="hanja8") return "Hanja level 8";
	if($s=="hanja7") return "Hanja level 7";
	if($s=="hanja6") return "Hanja level 6";
	if($s=="hanja5") return "Hanja level 5";
	if($s=="hanja4") return "Hanja level 4";
	if($s=="hanja3") return "Hanja level 3";
	if($s=="hanja2") return "Hanja level 2";
	if($s=="hanja1") return "Hanja level 1";
	if($s=="hanja1800a") return "Hanja part 1";
	if($s=="hanja1800b") return "Hanja part 2";
	if($s=="ku") return "Uncommon hanja";
	if($s=="kc") return "Components";
	if($s=="hanguljamos") return "Jamo";
	if($s=="hangulsyllables") return "Hangul";
	if($s=="hsk31") return "HSK v3 level 1, simplified hanzi";
	if($s=="hsk32") return "HSK v3 level 2, simplified hanzi";
	if($s=="hsk33") return "HSK v3 level 3, simplified hanzi";
	if($s=="hsk34") return "HSK v3 level 4, simplified hanzi";
	if($s=="hsk35") return "HSK v3 level 5, simplified hanzi";
	if($s=="hsk36") return "HSK v3 level 6, simplified hanzi";
	if($s=="hsk37") return "HSK v3 level 7, simplified hanzi";
	if($s=="hsk38") return "HSK v3 level 8, simplified hanzi";
	if($s=="hsk39") return "HSK v3 level 9, simplified hanzi";
	if($s=="frequentNotHsk3") return "Other frequent hanzi";
	if($s=="commonNotHsk3NorFrequent") return "Other common hanzi";
	if($s=="frequent2500") return "2500 frequent hanzi";
	if($s=="lessFrequent1000") return "1000 less frequent hanzi";
	if($s=="commonNotFrequent") return "3500 other common hanzi";
	if($s=="common7000") return "7000 common hanzi";
	if($s=="traditional") return "Traditional hanzi used in simplified Chinese";
	if($s=="uncommon") return "Uncommon hanzi";
	if($s=="component") return "Components";
	if($s=="t31") return "HSK v3 level 1, traditional hanzi";
	if($s=="t32") return "HSK v3 level 2, traditional hanzi";
	if($s=="t33") return "HSK v3 level 3, traditional hanzi";
	if($s=="t34") return "HSK v3 level 4, traditional hanzi";
	if($s=="t35") return "HSK v3 level 5, traditional hanzi";
	if($s=="t36") return "HSK v3 level 6, traditional hanzi";
	if($s=="t37") return "HSK v3 level 7, traditional hanzi";
	if($s=="t38") return "HSK v3 level 8, traditional hanzi";
	if($s=="t39") return "HSK v3 level 9, traditional hanzi";
	if($s=="taiwan4808") return "Taiwan 4808 common traditional hanzi";
	if($s=="tu") return "Other traditional hanzi";
	if($s=="tc") return "Components";
	if($s=="more") return "More";
	if($s=="radicals") return "The 214 radicals";
	if($s=="stroke") return "Strokes";
	return "";
}
function getOnelangSet($set)
{
	global $map;
	$s="[";
	$first=true;
	foreach($map[$set] as $b)
	{
		$c=getCharList($b);
		if($c)
		{
			if($first) $first=false;
			else $s.=",";
			$s.="{";
			$s.="\"title\":\"".makeTitle($b)."\",\"chars\":\"";
			$s.=getCharList($b);
			$s.="\"}";
		}
	}
	$s.="]";
	return $s;
}
if($set=="all")
{
	// do not mix characters of different languages
	$r="[";
	$lang="Ja";
	$s=getOnelangSet($lang);
	$r.="{\"lang\":\"".$lang."\",\"r\":".$s."}";
	$lang="Ko";
	$s=getOnelangSet($lang);
	$r.=",{\"lang\":\"".$lang."\",\"r\":".$s."}";
	$lang="ZhHans";
	$s=getOnelangSet($lang);
	$r.=",{\"lang\":\"".$lang."\",\"r\":".$s."}";
	$lang="ZhHant";
	$s=getOnelangSet($lang);
	$r.=",{\"lang\":\"".$lang."\",\"r\":".$s."}";
	$r.="]";
	echo $r;
}
else if($set=="Ja"||$set=="Ko"||$set=="ZhHans"||$set=="ZhHant")
	echo getOnelangSet($set);
else
{
	$s="[{";
	$s.="\"title\":\"\",\"chars\":\"";
	$s.=getCharList($set);
	$s.="\"}]";
	echo $s;
}

?>