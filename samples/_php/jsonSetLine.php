<?php
header("Content-Type: text/plain");
$myServer="192.168.1.23"; // to replace by your server domain or ip to enable save feature
if (($_SERVER['SERVER_NAME']=="localhost")||($_SERVER['SERVER_NAME']==$myServer))
{
	$data=(isset($_POST["data"])?$_POST["data"]:"");
	if (!$data) echo "NOK (no data)";
	else
	{
		$target=(isset($_POST["target"])?$_POST["target"]:"");
		if ($target&&file_put_contents($target,$data.PHP_EOL,FILE_APPEND|LOCK_EX))
			echo "OK (save done in ".$target.")";
		else echo "NOK (unable to write to ".$target." file)";
	}
}
else echo "NOK (not a convenient server)";
?>