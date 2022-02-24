# VideoColor PHP Search Client

This library is designed to find information about a movie and get the frame position using a screenshot from a video.
The number of free calls is limited. To remove restrictions, contact the site owner.

## About Video Color

All of us are faced with the task of finding information every day. You want to find text, images, audio or video information. Most often, text is used for a search query. Less commonly, images. There are services like "Shazam" that search for music using sound recording. We focused on building a search engine that searches for videos. We use images as parameters for the request.

[Site](https://www.videocolor.aapsoftware.ru "Video Color Site")

## Test

* Open terminal
* Go to the library folder with scripts, to where the sample.php file is located
* Type command

php sample.php test.jpg

You will see text like this

```
Title:  Round Midnight
Frame:  84155
Position:       0:58:29 (3509.97 sec)
Duration:       2:11:20 (7880.881 sec)
Producer:       Bertrand Tavernier
Country:
Creation year:  1986
Genre:  Drama, Music
Actors: Dexter Gordon, François Cluzet, Gabrielle Haker, Sandra Reaves-Phillips, Lonette McKee, Christine Pascal, Herbie Hancock, Bobby Hutcherson, Pierre Trabaud, Frédérique Meininger, Hart Leroy Bibbs, Liliane Rovère, Ged Marlon, Benoît Régent, Victoria Gabrielle Platt, Arthur French, John Berry, Martin Scorsese
IMDB:   http://www.imdb.com/title/tt0090557/
Kinopoisk:
Description:    In 'Round Midnight, real-life jazz legend Dexter Gordon brilliantly portrays the fictional tenor sax player Dale Turner, a musician slowly losing the battle with alcoholism, estranged from his family, and hanging on by a thread in the 1950's New York jazz world. Dale gets an offer to play in Paris, where, like many other black American musicians at the time, he enjoys a respect for his humanity that is not based upon the color of his skin. A Parisian man who is obsessed with Turner's music befriends him and attempts to save Turner from himself. Although for Dale the damage is already done, his poignant relationship with the man and his young daughter re-kindles his spirit and his music as the end draws near.
```

## Usage example

```PHP
use AapSoftware\VideoColor\SearchClient;

...

$img = imagecreatefromjpeg($fname);
$search = new AapSoftware\VideoColor\SearchClient();
$obj = $search->get($img);
imagedestroy($img);

if ($obj === null) {
    echo "Server not connected!\n";
    return;
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
```

