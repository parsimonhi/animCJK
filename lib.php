<?php
include_once __DIR__."/samples/_php/encoding.php";
include_once __DIR__."/samples/_php/unicode.php";
include_once __DIR__."/samples/_php/convertKana.php";

if( !function_exists('mb_str_split')){
	// some old php do not have it
    function mb_str_split(  $string = '', $length = 1 , $encoding = "UTF-8" ){
        if(!empty($string)){
            $split = array();
            $mb_strlen = mb_strlen($string,$encoding);
            for($pi = 0; $pi < $mb_strlen; $pi += $length){
                $substr = mb_substr($string, $pi,$length,$encoding);
                if( !empty($substr)){
                    $split[] = $substr;
                }
            }
        }
        return $split;
    }
}

function my_json_decode($line)
{
	// some old php do not have json_encode() and json_decode
	// decode a line from graphicsXxx.txt or dictionaryXxx.txt
	$a=new StdClass();
	if (preg_match("/^\\{\"character\":\"([^\"]+)\",\"strokes\":\\[\"([^\\]]+)\"\\],\"medians\":\\[(.+)\\]\\}$/",$line,$match))
	{
		$a->{'character'}=$match[1];
		$a->{'strokes'}=explode("\",\"",$match[2]);
		$x=explode("]],[[",$match[3]);
		$kmx=count($x);
		$x[0]=str_replace("[[","",$x[0]);
		$x[$kmx-1]=str_replace("]]","",$x[$kmx-1]);
		$y=array();
		for($kx=0;$kx<$kmx;$kx++)
		{
			$y=explode("],[",$x[$kx]);
			$kmy=count($y);
			for($ky=0;$ky<$kmy;$ky++)
			{
				$y[$ky]=explode(",",$y[$ky]);
			}
			$x[$kx]=$y;
		}
		$a->{'medians'}=$x;
	}
	else if (preg_match("/\"character\":\"([^\"]+)\"/",$line,$match))
	{
		$a->{'character'}=$match[1];
		if (preg_match("/\"set\":\\[\"([^\\]]+)\"\\]/",$line,$match))
			$a->{'set'}=explode("\",\"",$match[1]);
		if (preg_match("/\"definition\":\"([^\"]+)\"/",$line,$match))
			$a->{'definition'}=$match[1];
		if (preg_match("/\"pinyin\":\\[\"([^\\]]+)\"\\]/",$line,$match))
			$a->{'pinyin'}=explode("\",\"",$match[1]);
		if (preg_match("/\"on\":\\[\"([^\\]]+)\"\\]/",$line,$match))
			$a->{'on'}=explode("\",\"",$match[1]);
		if (preg_match("/\"kun\":\\[\"([^\\]]+)\"\\]/",$line,$match))
			$a->{'kun'}=explode("\",\"",$match[1]);
		if (preg_match("/\"radical\":\"([^\"]+)\"/",$line,$match))
			$a->{'radical'}=$match[1];
		if (preg_match("/\"decomposition\":\"([^\"]+)\"/",$line,$match))
			$a->{'decomposition'}=$match[1];
		if (preg_match("/\"acjk\":\"([^\"]+)\"/",$line,$match))
			$a->{'acjk'}=$match[1];
	}
	return $a;
}

function transformPathFromGraphics($p)
{
	if (preg_match_all("#([MQCLZ ]+)([0-9.-]+) ([0-9.-]+)#",$p,$m))
	{
		$npm=count($m[0]);
		$q="";
		for ($np=0;$np<$npm;$np++)
		{
			$x=intval($m[2][$np]);
			$y=-(intval($m[3][$np])-900);
			$q.=$m[1][$np].$x." ".$y;
		}
		if (preg_match("/Z/",$p)) $q.="Z";
		return $q;
	}
	return $p;
}

function buildSvg($a)
{
	$u=decUnicode($a->{'character'});
	$id="z".$u;
	$x="xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\"";
	$s="<svg id=\"".$id."\" class=\"acjk\" version=\"1.1\" viewBox=\"0 0 1024 1024\" ".$x.">\n";
	
	// style
	$s.="<style>\n<![CDATA[\n";
	$s.="@keyframes zk {\n";
	$s.="\tto {\n";
	$s.="\t\tstroke-dashoffset:0;\n";
	$s.="\t}\n";
	$s.="}\n";
	$s.="svg.acjk path[clip-path] {\n";
	$s.="\t--t:0.8s;\n";
	$s.="\tanimation:zk var(--t) linear forwards var(--d);\n";
	$s.="\tstroke-dasharray:3337;\n"; // more than pathLength + 1
	$s.="\tstroke-dashoffset:3339;\n"; // less than 2 * strokeDasharray - pathLength
	$s.="\tstroke-width:128;\n"; // acjk.strokeWidthMax + 8 or 16?
	$s.="\tstroke-linecap:round;\n";
	$s.="\tfill:none;\n";
	$s.="\tstroke:#000;\n";
	$s.="}\n";
	$s.="svg.acjk path[id] {fill:#ccc;}\n";
	$s.="]]>\n</style>\n";

	// stroke shapes
	$k=0;
	foreach($a->{'strokes'} as $p)
	{
		$k++;
		$p=str_replace(","," ",$p);
		$p=preg_replace("#\s?([MQCLZ])\s?#","$1",$p);
		$p=preg_replace("#([^ ])-#","$1 -",$p);
		// transform coordinates of path nodes (x2 = x1, y2 = 900-y1)
		// don't do this transformation if $_GET["t"] exists and is not 1
		if (!isset($_GET["t"])||($_GET["t"]==1)) $p=transformPathFromGraphics($p);
		$s.="<path id=\"".$id."d".$k."\" d=\"".$p."\"/>\n";
	}
	
	// clip paths
	$s.="<defs>\n";
	$k=0;
	foreach($a->{'strokes'} as $p)
	{
		$k++;
		$s.="\t<clipPath id=\"".$id."c".$k."\">";
		$s.="<use xlink:href=\"#".$id."d".$k."\"/>";
		$s.="</clipPath>\n";
	}
	$s.="</defs>\n";
	
	// medians
	$k=0;
	foreach($a->{'medians'} as $m)
	{
		$k++;
		$z="";
		foreach($m as $point) $z.=($z?"L":"M").$point[0]." ".$point[1];
		if (!isset($_GET["t"])||($_GET["t"]==1)) $z=transformPathFromGraphics($z);
		$s.="<path style=\"--d:".$k."s;\" pathLength=\"3333\" clip-path=\"url(#".$id."c".$k.")\" d=\"".$z."\"/>\n";
	}
	
	$s.="</svg>";
	return $s;
}
?>