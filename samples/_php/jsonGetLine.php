<?php
header("Content-Type: text/plain");
$data=(isset($_POST["data"])?$_POST["data"]:"");
$source=(isset($_POST["source"])?$_POST["source"]:"");
if (!$data) echo "Error: no data";
else if ($handle=fopen($source,"r"))
{
	while ((($line=fgets($handle))!==false)&&(mb_strpos($line,$data,0,'UTF-8')===false));
	fclose($handle);
	if ($line) echo trim(preg_replace('/\s+/',' ',$line));
	else echo "Error: \"".$data."\" not found in ".$source;
}
else echo "Error: cannot open ".$source;
?>