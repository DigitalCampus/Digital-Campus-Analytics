<?php 
include_once('config.php');

$image = required_param('image',PARAM_TEXT);
$size = optional_param('size',200,PARAM_INTEGER);

$imageurl = rawurldecode($image);
$imagefilename = md5($imageurl).".jpg";

// find if original file size has been downloaded - if not then create it
if (!file_exists($CONFIG->imagecache.$imagefilename)){
	copy($imageurl, $CONFIG->imagecache.$imagefilename );
}

// find if resized image exists - if not then create it
if (!file_exists($CONFIG->imagecache.$size."-".$imagefilename)){
	$fullimage = imagecreatefromjpeg($CONFIG->imagecache.$imagefilename);
	
	$width = imagesx($fullimage);
    $height = imagesy($fullimage);
	$new_width = floatval($size);
    $new_height = $height * ($new_width/$width);

    // Resample
    $image_resized = imagecreatetruecolor($new_width, $new_height);
    imagecopyresampled($image_resized, $fullimage, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
    
    //save to the cache
    imagejpeg($image_resized,$CONFIG->imagecache.$size."-".$imagefilename);
    
}

// now display the final image
if (file_exists($CONFIG->imagecache.$size."-".$imagefilename)){
	header('Content-type: image/jpeg');
    $imagejpeg = imagecreatefromjpeg($CONFIG->imagecache.$size."-".$imagefilename);
    imagejpeg($imagejpeg);
	imagedestroy($imagejpeg);
    die();
} 

?>