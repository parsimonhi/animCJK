<?php
include_once "encoding.php";
include_once "getCharList.php";
$input=json_decode(file_get_contents('php://input'),true);
if(isset($input["s"])) $set=$input["s"];
else $set="Ja";
$s="";
function makeTitle($s)
{
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
	if($s=="hsk1") return "HSK 1, simplified hanzi";
	if($s=="hsk2") return "HSK 2, simplified hanzi";
	if($s=="hsk3") return "HSK 3, simplified hanzi";
	if($s=="hsk4") return "HSK 4, simplified hanzi";
	if($s=="hsk5") return "HSK 5, simplified hanzi";
	if($s=="hsk6") return "HSK 6, simplified hanzi";
	if($s=="hsk6") return "HSK 6, simplified hanzi";
	if($s=="frequentNotHsk") return "Other frequent hanzi";
	if($s=="commonNotHskNorFrequent") return "Other common hanzi";
	if($s=="traditional") return "Traditional hanzi";
	if($s=="uncommon") return "Uncommon hanzi";
	if($s=="component") return "Components";
	if($s=="traditional1") return "HSK 1, traditional hanzi";
	if($s=="traditional2") return "HSK 2, traditional hanzi";
	if($s=="traditional3") return "HSK 3, traditional hanzi";
	if($s=="traditional4") return "HSK 4, traditional hanzi";
	if($s=="traditional5") return "HSK 5, traditional hanzi";
	if($s=="traditional6") return "HSK 6, traditional hanzi";
	if($s=="stroke") return "Strokes";
	return "";
}
if($set=="Ja") $a=["g1","g2","g3","g4","g5","g6","g7","g8","g9","gc","stroke"];
else if($set=="Ko") $a=["hanja8","hanja7","hanja6","hanja5","hanja4","hanja3","hanja2","hanja1"];
else if($set=="ZhHans") $a=["hsk1","hsk2","hsk3","hsk4","hsk5","hsk6","frequentNotHsk","commonNotHskNorFrequent","uncommon","traditional","component","stroke"];
else if($set=="ZhHant") $a=["traditional1","traditional2","traditional3"];
if($set=="Ja"||$set=="Ko"||$set=="ZhHans"||$set=="ZhHant")
{
	$s.="[";
	
	$first=true;
	foreach($a as $b)
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
}
else
{
	$s.="[{";
	$s.="\"title\":\"\",\"chars\":\"";
	$s.=getCharList($set);
	$s.="\"}]";
}
echo $s;
?>