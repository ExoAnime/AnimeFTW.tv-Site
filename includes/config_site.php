<?php

// NewSystem DB connect
//$newsdbhost = 'localhost';
$newsdbhost = 'localhost';
$newsdbuser = 'mainaftw_anime';
$newsdbpass = 'm2Igd@9W;P8!';
$newsdbname = 'mainaftw_anime';
if($_SERVER['HTTP_HOST'] == 'v4.aftw.ftwdevs.com'||$_SERVER['HTTP_HOST'] == 'hani.v4.aftw.ftwdevs.com')
{
	// this will be for development connections only.
	$newsdbhost 		= 'localhost';
	$newsdbuser 		= 'devsftw9_anime';
	$newsdbpass 		= 'L=.zZ76[,TOqwf*&tl';
	$newsdbname 		= 'devsftw9_anime';
}
$conn = mysqli_connect($newsdbhost,$newsdbuser,$newsdbpass,$newsdbname);
mysqli_set_charset($conn,"utf8");
?>
