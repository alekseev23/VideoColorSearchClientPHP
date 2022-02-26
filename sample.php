<?php

/**
 * Sample script for search information about the video by screenshot
 */

declare(strict_types=1);

use AapSoftware\VideoColor\exceptions\ApiException;

require("vendor/autoload.php");

// Check input parameters
if ($argc < 2) {
    echo "Usage: c:\\php\\php sample.php image\n";
    echo "Example: c:\\php\\php sample.php test.jpg\n";
    return;
}

$fname = $argv[1];
if (!file_exists($fname)) {
    echo "Input file not found!\n";
    return;
}

try {
    $search = new AapSoftware\VideoColor\SearchClient();
    $obj = $search->find($fname);

    if ($obj->result) {
        echo "Title:\t", $obj->title, "\n";
        echo "Frame:\t", $obj->frame, "\n";
        echo "Position:\t", $obj->position, "\n";
        echo "Duration:\t", $obj->duration, "\n";
        echo "Producer:\t", $obj->producer, "\n";
        echo "Country:\t", $obj->country, "\n";
        echo "Creation year:\t", $obj->creation_year, "\n";
        echo "Genre:\t", $obj->genre, "\n";
        echo "Actors:\t", $obj->actors, "\n";
        echo "IMDB:\t", $obj->imdb, "\n";
        echo "Kinopoisk:\t", $obj->kinopoisk, "\n";
        echo "Description:\t", $obj->description, "\n";
    } else {
        echo "Not found\n";
    }
} catch (ApiException $e) {
    echo "Server not connected!\n";
    echo $e->getMessage(), "\n";
}
