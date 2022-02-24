<?php

/**
 * Sample script for search information about the video by screenshot
 */

declare(strict_types=1);
 
require("src/SearchClient.php");

// Check input parameters
if ($argc < 2) {
	echo "Usage: c:\\php\\php sample.php image\n"
		."Example: c:\\php\\php sample.php test.jpg\n";
	return;
}

$fname = $argv[1];
if (!file_exists($fname)) {
    echo "Input file not found!\n";
    return;
}
$img = imagecreatefromjpeg($fname);
$search = new AapSoftware\VideoColor\SearchClient();
$obj = $search->get($img);
imagedestroy($img);

if ($obj === null) {
    echo "Server not connected!\n";
} elseif (!$obj->result) {
	echo "Not found\n";
} else {
	echo "Title:\t" . $obj->title . "\n";
	echo "Frame:\t" . $obj->frame."\n";
	echo "Position:\t" . $obj->position . "\n";
	echo "Duration:\t" . $obj->duration . "\n";
	echo "Producer:\t".$obj->producer."\n";
	echo "Country:\t".$obj->country."\n";
	echo "Creation year:\t".$obj->creation_year."\n";
	echo "Genre:\t".$obj->genre."\n";
	echo "Actors:\t".$obj->actors."\n";
	echo "IMDB:\t".$obj->imdb."\n";
	echo "Kinopoisk:\t".$obj->kinopoisk."\n";
	echo "Description:\t".$obj->description."\n";
}
