<?php
function unichr($u)
{
	// return the UTF-8 char corresponding to the decimal unicode $u
    return mb_convert_encoding('&#' . intval($u) . ';', 'UTF-8', 'HTML-ENTITIES');
}
function decUnicode($u)
{
	// return the decimal unicode of UTF-8 char $u
	$len=strlen($u);
	if ($len==0) return 63;
	$r1=ord($u[0]);
	if ($len==1) return $r1;
	$r2=ord($u[1]);
	if ($len==2) return (($r1&31)<< 6)+($r2&63);
	$r3=ord($u[2]);
	if ($len==3) return (($r1&15)<<12)+(($r2&63)<< 6)+($r3&63);
	$r4=ord($u[3]);
	if ($len==4) return (($r1& 7)<<18)+(($r2&63)<<12)+(($r3&63)<<6)+($r4&63);
	return 63;
}
function displayHeader($title)
{
	// display header, can be anything else
	global $lang,$dec,$data;
	echo "<header>\n";
	echo "<h1>".$title."</h1>\n";
	echo "<p>";
	if ($title=="AnimCJK - Anime several") echo $data;
	else echo unichr($dec)." (".$dec.")";
	echo " - ".(($lang=="ja")?"Japanese":"Simplified Chinese");
	echo "</p>\n";
	echo "</header>\n";
}
function displayFooter($sample)
{
	// display footer, can be anything else
	global $lang,$dec,$data;
	echo "<footer>\n";
	echo "<a href=\"./";
	echo "?sample=".$sample;
	if (isset($_POST["lang"])) echo "&lang=".$_POST["lang"];
	if (isset($_POST["dec"])) echo "&dec=".$_POST["dec"];
	if (isset($_POST["data"])) echo "&data=".$_POST["data"];
	echo "\">Sample selector</a>\n";
	echo "- <a href=\"../licenses/COPYING.txt\">Licences</a><br>\n";
	echo "Copyright 2016-2018 - François Mizessyn\n";
	echo "</footer>\n";
}
// Set some global variables
// $lang (language code: ja or zh-hans)
// $dir (directory where svg corresponding to the given language are stored)
// $dec (decimal unicode of the character to be displayed)
// $data (data to be displayed)
// Usage of $dec or $data as source depends of the sample script
// $defaultChar (default character: 漢/28450 in Japanese, 汉/27721 in simplified Chinese)
// Display the default character if:
//   1) there is no "dec" post data
//   2) $dec is not a numeric
//   3) the character corresponding to $dec is not in $dir
if (isset($_POST["lang"])) {$lang=$_POST["lang"];if ($lang!="zh-hans") $lang="ja";}
else $lang="ja";
if ($lang=="ja") {$dir="svgsJa";$defaultChar="28450";}
else {$dir="svgsZhHans";$defaultChar="27721";}
if (isset($_POST["dec"])) $dec=$_POST["dec"];
else $dec=$defaultChar;
if (!is_numeric($dec)||!file_exists("../".$dir."/".$dec.".svg")) $dec=$defaultChar;
if (isset($_POST["data"]))
{
	$data=$_POST["data"];
	if ($data=="") $data=unichr($dec);
}
else $data=unichr($dec);
$sample=basename($_SERVER['PHP_SELF'],".php");
?>
