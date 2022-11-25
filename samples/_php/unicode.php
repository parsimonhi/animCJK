<?php
function unichr($u)
{
	// return a char from its decimal unicode
    return mb_convert_encoding('&#' . intval($u) . ';', 'UTF-8', 'HTML-ENTITIES');
}

function decUnicode($c)
{
	// return the decimal unicode of a char
	$len=strlen($c);
	if ($len==0) return 63;
	$r1=ord($c[0]);
	if ($len==1) return $r1;
	$r2=ord($c[1]);
	if ($len==2) return (($r1&31)<< 6)+($r2&63);
	$r3=ord($c[2]);
	if ($len==3) return (($r1&15)<<12)+(($r2&63)<< 6)+($r3&63);
	$r4=ord($c[3]);
	if ($len==4) return (($r1& 7)<<18)+(($r2&63)<<12)+(($r3&63)<<6)+($r4&63);
	return 63;
}

function hexUnicode($c)
{
	// return the hexa unicode of a char
	return str_pad(dechex(decUnicode($c)),5,"0",STR_PAD_LEFT);
}

function unihanUnicode($c)
{
	// return the unicode of a char as in the unihan dictionary
	return "U+".strtoupper(dechex(decUnicode($c)));
}
?>
