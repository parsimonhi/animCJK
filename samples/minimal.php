<?php
include_once "_php/encoding.php";
include_once "_php/unicode.php";

// Set some global variables
// $lang (language code: ja, zh-hans or zh-hant)
// $dir (directory where svg corresponding to the given language are stored)
// $dec (decimal unicode of the character to be displayed)
// $data (data to be displayed)
// Usage of $dec or $data as source depends of the sample script
// $defaultChar (default character: 漢/28450 in ja and zh-hant, 汉/27721 in zh-hans)
// Display the default character if:
//   1) there is no "dec" post data
//   2) $dec is not a numeric
//   3) the character corresponding to $dec is not in $dir
if (isset($_POST["lang"]))
{
	if ($_POST["lang"]=="zh-hant") {$lang="zh-hant";$dir="svgsZhHant";$defaultChar="28450";}
	else if ($_POST["lang"]=="zh-hans") {$lang="zh-hans";$dir="svgsZhHans";$defaultChar="27721";}
	else {$lang="ja";$dir="svgsJa";$defaultChar="28450";}
}
if (isset($_POST["dec"])) $dec=$_POST["dec"];
else if (isset($_POST["data"])) $dec=decUnicode($_POST["data"]);
else $dec=$defaultChar;
if (!is_numeric($dec)||!file_exists("../".$dir."/".$dec.".svg")) $dec=$defaultChar;
if (isset($_POST["data"]))
{
	$data=$_POST["data"];
	if ($data=="") $data=unichr($dec);
}
else $data=unichr($dec);
$sample=basename($_SERVER['PHP_SELF'],".php");

function displayHeader($title)
{
	// display header, can be anything else
	global $lang,$dec,$data,$language;
	echo "<header>\n";
	echo "<h1>".$title."</h1>\n";
	echo "<p>";
	if ($title=="AnimCJK - Anime several") echo $data;
	else echo unichr($dec)." (".$dec.")";
	if ($lang=="zh-hant") $language="Traditional Chinese";
	else if ($lang=="zh-hans") $language="Simplified Chinese";
	else $language="Japanese";
	echo " - ".$language;
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
	echo "- <a href=\"../licenses/COPYING.txt\">Licences</a>\n";
	echo "</footer>\n";
}
?>
