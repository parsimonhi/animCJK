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
	if($s=="gs") return "Strokes";
	if($s=="hsk1") return "HSK 1";
	if($s=="hsk2") return "HSK 2";
	if($s=="hsk3") return "HSK 3";
	if($s=="hsk4") return "HSK 4";
	if($s=="hsk5") return "HSK 5";
	if($s=="hsk6") return "HSK 6";
	if($s=="hsk6") return "HSK 6";
	if($s=="frequentNotHsk") return "Other frequent hanzi";
	if($s=="commonNotHskNorFrequent") return "Other common hanzi";
	if($s=="uncommon") return "Uncommon hanzi";
	if($s=="component") return "Components";
	if($s=="stroke") return "Strokes";
	return "";
}
if($set=="Ja") $a=["g1","g2","g3","g4","g5","g6","g7","g8","g9","gc","gs"];
else if($set=="ZhHans") $a=["hsk1","hsk2","hsk3","hsk4","hsk5","hsk6","frequentNotHsk","commonNotHskNorFrequent","uncommon","component","stroke"];
if($set=="Ja"||$set=="ZhHans")
{
	$s.="[";
	
	$first=true;
	foreach($a as $b)
	{
		if($first) $first=false;
		else $s.=",";
		$s.="{";
		$s.="\"title\":\"".makeTitle($b)."\",\"chars\":\"";
		$s.=getCharList($b);
		$s.="\"}";
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