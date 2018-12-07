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

function getSvgChar($char,$lang="zh-hans")
{
	// char: character to display
	// lang: used to select convenient svg folders
	$dec=decUnicode($char);
	if (strtolower($lang)=="ja")
	{
		if (file_exists("svgsKana/".$dec.".svg")) $f="svgsKana/".$dec.".svg";
		else if (file_exists("svgsJa/".$dec.".svg")) $f="svgsJa/".$dec.".svg";
		else $f=null;
	}
	else if (strtolower($lang)=="zh-hant")
	{
		if (file_exists("svgsZhHant/".$dec.".svg")) $f="svgsZhHant/".$dec.".svg";
		else $f=null;
	}
	else // default (zh-hans)
	{
		if (file_exists("svgsZhHans/".$dec.".svg")) $f="svgsZhHans/".$dec.".svg";
		else $f=null;
	}
	if ($f) $s=file_get_contents($f);
	else $s="";
	if ($s) return $s;
	else
	{
		if (strtolower($lang)=="ja") return errorSvg(3,$lang,$char." is not in","our Japanese","repository");
		if (strtolower($lang)=="zh-hant") return errorSvg(4,$lang,$char." is not in","our traditional","Chinese","repository");
		return errorSvg(4,$lang,$char." is not in","our simplified","Chinese","repository");
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