<?php

declare(strict_types=1);

namespace AapSoftware\VideoColor;

use AapSoftware\VideoColor\exceptions\FileNotFoundException;

class ImageEncoder
{
    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;

        if (!file_exists($path)) {
            throw new FileNotFoundException($path);
        }
    }

    public function encode(): string
    {
        $img = imagecreatefromjpeg($this->path);

        $M = 11;
        $N = 7;

        $imgSx = imagesx($img);
        $imgSy = imagesy($img);

        if ($imgSx >= $imgSy) {
            $areaSx = $imgSy;
            $areaX0 = intdiv($imgSx - $areaSx, 2);
            $areaY0 = 0;
        } else {
            $areaSx = $imgSx;
            $areaX0 = 0;
            $areaY0 = intdiv($imgSy - $areaSx, 2);
        }

        for ($i = 0; $i < 4; $i++) {
            $boxX[$i] = $areaX0 + intdiv($areaSx * $i, 4);
            $boxY[$i] = $areaY0 + intdiv($areaSx * $i, 4);
            $boxSx[$i] = $areaX0 + intdiv($areaSx * ($i + 1), 4) - $boxX[$i];
        }

        $distance = 1. * $areaSx / $N;
        $size = 0.5 * $areaSx / $N;
        $size2 = 0.5 * $size;

        $X = [];
        $Y = [];

        $CM = intdiv($M, 2);
        $CN = intdiv($N, 2);

        for ($i = 0; $i < $M; $i++) {
            $X[$i] = intval(0.5 * $imgSx + $distance * ($i - $CM) - $size2);
        }

        for ($i = 0; $i < $N; $i++) {
            $Y[$i] = intval(0.5 * $imgSy + $distance * ($i - $CN) - $size2);
        }

        if ($X[0] >= 0) {
            $imageType = 3;
        } elseif ($X[1] >= 0) {
            $imageType = 2;
        } else {
            $imageType = 1;
        }

        $res = $imageType . ',';

        $res .= $this->countMiddleSum($img, $boxX[1], $boxY[0], $boxSx[1], $boxSx[0]) . ',';
        $res .= $this->countMiddleSum($img, $boxX[0], $boxY[1], $boxSx[0], $boxSx[1]) . ',';
        $res .= $this->countMiddleSum($img, $boxX[1], $boxY[1], $boxSx[1], $boxSx[1]) . ',';

        $res .= $this->countMiddleSum($img, $boxX[2], $boxY[0], $boxSx[2], $boxSx[0]) . ',';
        $res .= $this->countMiddleSum($img, $boxX[2], $boxY[1], $boxSx[2], $boxSx[1]) . ',';
        $res .= $this->countMiddleSum($img, $boxX[3], $boxY[1], $boxSx[3], $boxSx[1]) . ',';

        $res .= $this->countMiddleSum($img, $boxX[0], $boxY[2], $boxSx[0], $boxSx[2]) . ',';
        $res .= $this->countMiddleSum($img, $boxX[1], $boxY[2], $boxSx[1], $boxSx[2]) . ',';
        $res .= $this->countMiddleSum($img, $boxX[1], $boxY[3], $boxSx[1], $boxSx[3]) . ',';

        $res .= $this->countMiddleSum($img, $boxX[2], $boxY[2], $boxSx[2], $boxSx[2]) . ',';
        $res .= $this->countMiddleSum($img, $boxX[3], $boxY[2], $boxSx[3], $boxSx[2]) . ',';
        $res .= $this->countMiddleSum($img, $boxX[2], $boxY[3], $boxSx[2], $boxSx[3]) . ',';

        $size3 = intval(round($size));
        for ($j = 0; $j < $N; $j++) {
            for ($i = 0; $i < $M; $i++) {
                $x = $X[$i];
                $y = $Y[$j];

                if (($x < 0) || ($x + $size3 > $imgSx) || ($y < 0) || ($y + $size3 > $imgSy)) {
                    $res .= '0,';
                } else {
                    $res .= $this->countMiddleSum($img, $x, $y, $size3, $size3) . ',';
                }
            }
        }

        imagedestroy($img);

        return $res;
    }

    /**
     * Average value of sums
     */
    private function countMiddleSum(&$img, int $x0, int $y0, int $sx, int $sy): int
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

        return intdiv($sum + 3 * $k2, 3 * $k);
    }
}