<?php
header("Content-Type: text/plain");

include "lib.php";

function errorSvg($n,$lang,$char1,$char2="",$char3="",$char4="")
{
	$s="<svg lang=".$lang." class=\"error\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 1024 1024\">\n";
	if ($n==1)
	{
		$s.="<text x=\"50%\" y=\"50%\" text-anchor=\"middle\" dominant-baseline=\"central\" font-size=\"128\">";
		$s.=$char1;
		$s.="</text>\n";
	}
	else if ($n==2)
	{
		$s.="<text x=\"50%\" y=\"40%\" text-anchor=\"middle\" dominant-baseline=\"central\" font-size=\"128\">";
		$s.=$char1;
		$s.="</text>\n";
		$s.="<text x=\"50%\" y=\"60%\" text-anchor=\"middle\" dominant-baseline=\"central\" font-size=\"128\">";
		$s.=$char2;
		$s.="</text>\n";
	}
	else if ($n==3)
	{
		$s.="<text x=\"50%\" y=\"30%\" text-anchor=\"middle\" dominant-baseline=\"central\" font-size=\"128\">";
		$s.=$char1;
		$s.="</text>\n";
		$s.="<text x=\"50%\" y=\"50%\" text-anchor=\"middle\" dominant-baseline=\"central\" font-size=\"128\">";
		$s.=$char2;
		$s.="</text>\n";
		$s.="<text x=\"50%\" y=\"70%\" text-anchor=\"middle\" dominant-baseline=\"central\" font-size=\"128\">";
		$s.=$char3;
		$s.="</text>\n";
	}
	else if ($n==4)
	{
		$s.="<text x=\"50%\" y=\"20%\" text-anchor=\"middle\" dominant-baseline=\"central\" font-size=\"128\">";
		$s.=$char1;
		$s.="</text>\n";
		$s.="<text x=\"50%\" y=\"40%\" text-anchor=\"middle\" dominant-baseline=\"central\" font-size=\"128\">";
		$s.=$char2;
		$s.="</text>\n";
		$s.="<text x=\"50%\" y=\"60%\" text-anchor=\"middle\" dominant-baseline=\"central\" font-size=\"128\">";
		$s.=$char3;
		$s.="</text>\n";
		$s.="<text x=\"50%\" y=\"80%\" text-anchor=\"middle\" dominant-baseline=\"central\" font-size=\"128\">";
		$s.=$char4;
		$s.="</text>\n";
	}
	else $s.="Error";
	$s.="</svg>\n";
	return $s;
}

function getSvgChar($char,$lang="zh-Hans")
{
	// char: character to display
	// lang: used to select convenient svg folders
	$dec=decUnicode($char);
	if ($lang=="ja")
	{
		if (file_exists("svgsJa/".$dec.".svg")) $f="svgsJa/".$dec.".svg";
		else $f=null;
	}
	else // default (zh-Hans)
	{
		if (file_exists("svgsZhHans/".$dec.".svg")) $f="svgsZhHans/".$dec.".svg";
		else $f=null;
	}
	if ($f) $s=file_get_contents($f);
	else $s="";
	if ($s) return $s;
	else
	{
		if ($lang=="ja") return errorSvg(2,$lang,$char." is not a jōyō","or jinmeyō kanji");
		else return errorSvg(4,$lang,$char." is not in","HSK nor a","frequently used","simplified hanzi");
	}
}

if (isset($_POST["data"])&&$_POST["data"])
{
	if (isset($_POST["lang"])&&$_POST["lang"]) $lang=$_POST["lang"];
	else $lang="zh";
	$char=$_POST["data"];
	echo getSvgChar($char,$lang)."\n";
}
else echo errorSvg(4,"en","No data!","Click on","a character","in the list below");

?>