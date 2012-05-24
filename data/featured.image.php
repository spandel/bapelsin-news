<?php
                                
$url=$_GET['url'];

$str=substr($_GET['url'], -2, 1);
/*
jpg
jpeg
png
gif*/
$format="jpg";




switch($str)
{
	//jpg
case 'p':
	$src=imagecreatefromjpeg($url);
	break;
	//jpeg
case 'e':
	$src=imagecreatefromjpeg($url);
	break;
	//png
case 'n':
	$format='png';
	$src=imagecreatefrompng($url);
	break;
	//gif
case 'i':
	$format='gif';
	$src=imagecreatefromgif($url);
	break;
}

$dest=imagecreatetruecolor(300,150);

imagecopy($dest, $src, 0,0, $_GET['x'],$_GET['y'],300,150);

header('Content-Type: image/'.$format);
imagegif($dest);

imagedestroy($dest);
imagedestroy($src);
?>
