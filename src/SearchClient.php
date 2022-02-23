<?php

/**
 * Finding information about the movie and the exact position
 * of the frame in the video from the screenshot
 */

 declare(strict_types=1);
 
namespace AapSoftware\VideoColor;

class SearchClient {
	
	/**
	 * Analyze image and send query
	 */
	public function get($img, string $lang = "en"): ?object
	{
		$query = $this->prepare($img);
		if ($query === null) return(null);
		$result = $this->send($query, $lang);
		if ($result === null) return(null);
		return($result);
	}

	/**
 	* Send search query
 	*/
	private function send(string $query, string $lang): ?object
	{
		$params = ["query" => $query];
		if ($lang == "en") $url = "https://www.videocolor.aapsoftware.ru/v4/en/find.php";
		else $url = "https://www.videocolor.aapsoftware.ru/v4/ru/find.php";
		$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_POST, true);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	$data = curl_exec($ch);
    	$code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    	curl_close($ch);
		if ($code != 200) return(null);
		return(json_decode($data));
	}

	/**
	 * Analyze image
	 */
	private function prepare(&$img): ?string 
	{
		$M = 11;
		$N = 7;
		$img_sx = imagesx($img);
		$img_sy = imagesy($img);
		if ($img_sx >= $img_sy) {
			$area_sx = $img_sy;
			$area_x0 = intdiv($img_sx - $area_sx, 2);
			$area_y0 = 0;
		}
		else {
			$area_sx = $img_sx;
			$area_x0 = 0;
			$area_y0 = intdiv($img_sy - $area_sx, 2);
		}
		if ($img_sx >= $img_sy) {
			$area_sx = $img_sy;
			$area_x0 = intdiv($img_sx - $area_sx, 2);
			$area_y0 = 0;
		}
		else {
			$area_sx = $img_sx;
			$area_x0 = 0;
			$area_y0 = intdiv($img_sy - $area_sx, 2);
		}

		for ($i=0; $i<4; $i++) {
			$box_x[$i] = $area_x0 + intdiv($area_sx * $i, 4);
			$box_y[$i] = $area_y0 + intdiv($area_sx * $i, 4);
			$box_sx[$i] = $area_x0 + intdiv($area_sx * ($i + 1), 4) - $box_x[$i];
		}
		$res="";

		$res .= $this->countMiddleSum($img, $box_x[1], $box_y[0], $box_sx[1], $box_sx[0]) . ",";
		$res .= $this->countMiddleSum($img, $box_x[0], $box_y[1], $box_sx[0], $box_sx[1]) . ",";
		$res .= $this->countMiddleSum($img, $box_x[1], $box_y[1], $box_sx[1], $box_sx[1]) . ",";

		$res .= $this->countMiddleSum($img, $box_x[2], $box_y[0], $box_sx[2], $box_sx[0]) . ",";
		$res .= $this->countMiddleSum($img, $box_x[2], $box_y[1], $box_sx[2], $box_sx[1]) . ",";
		$res .= $this->countMiddleSum($img, $box_x[3], $box_y[1], $box_sx[3], $box_sx[1]) . ",";

		$res .= $this->countMiddleSum($img, $box_x[0], $box_y[2], $box_sx[0], $box_sx[2]) . ",";
		$res .= $this->countMiddleSum($img, $box_x[1], $box_y[2], $box_sx[1], $box_sx[2]) . ",";
		$res .= $this->countMiddleSum($img, $box_x[1], $box_y[3], $box_sx[1], $box_sx[3]) . ",";

		$res .= $this->countMiddleSum($img, $box_x[2], $box_y[2], $box_sx[2], $box_sx[2]) . ",";
		$res .= $this->countMiddleSum($img, $box_x[3], $box_y[2], $box_sx[3], $box_sx[2]) . ",";
		$res .= $this->countMiddleSum($img, $box_x[2], $box_y[3], $box_sx[2], $box_sx[3]) . ",";

		$distance = 1. * $area_sx / $N;
		$distance2 = 0.5 * $distance;
		$size = 0.5 * $area_sx / $N;
		$size2 = 0.5 * $size;

		$X=[];
		$Y=[];

		$CM=intdiv($M, 2);
		$CN=intdiv($N, 2);
		for ($i = 0; $i < $M; $i++) $X[$i] = intval(0.5 * $img_sx + $distance * ($i-$CM) - $size2);
		for ($i = 0; $i < $N; $i++) $Y[$i] = intval(0.5 * $img_sy + $distance * ($i-$CN) - $size2);
		if ($X[0] >= 0) $imagetype = 3;
		elseif ($X[1] >= 0) $imagetype=2;
		else $imagetype=1;
		$res = $imagetype . "," . $res;

		$size3=intval(round($size));
		for ($j = 0; $j < $N; $j++) {
			for ($i = 0; $i < $M; $i++) {
				$x = $X[$i];
				$y = $Y[$j];
				if (($x < 0)||($x + $size3 > $img_sx)||($y < 0)||($y + $size3 > $img_sy)) $res .= "0,";
				else $res .= $this->countMiddleSum($img, $x, $y, $size3, $size3) . ",";
			}
		}
		return($res);
	}

	/**
	 * Average value of sums
	 */
	private function countMiddleSum(&$img, int $x0, int $y0, int $sx, int $sy) :int
	{
		$sum = 0;
		$k = $sx * $sy;
		$k2 = intdiv($k, 2);
		for ($j = $y0; $j < $y0 + $sy; $j++) {
			for ($i = $x0; $i < $x0 + $sx; $i++) {
				$c = imagecolorat($img, $i, $j);
				$sum += $c & 255;
				$sum += ($c >> 8) & 255;
				$sum += ($c >> 16) & 255;
			}
		}
		$res = intdiv($sum + 3 * $k2, 3 * $k);
		return($res);
	}

}
