<?php

// FUNGSI XOR
function _xor($text, $key)
{
    for ($i = 0; $i < strlen($text); $i++) {
        $text[$i] = intval($text[$i]) ^ intval($key[$i]);
    }
    return $text;
}

// FUNGSI ROTASI KE KANAN
function ROL($binner, $shift)
{
    $length = strlen($binner);
    for ($i = 0; $i < $shift; $i++) {
        $x      = substr($binner, 0, 1);
        $temp   = substr($binner, 1, $length - 1) . $x;
        $binner = $temp;
    }

    return $binner;
}

// FUNGSI ROTASI KE KIRI
function ROR($binner, $shift)
{
    $length = strlen($binner);
    for ($i = 0; $i < $shift; $i++) {
        $x      = substr($binner, -1);
        $temp   = $x . substr($binner, 0, $length - 1);
        $binner = $temp;
    }
}

function blockConverter($kalimat)
{
    $length  = strlen($kalimat);
    $encoded = array();
    for ($i = 0; $i < $length; $i++) {
        $temp = decbin(ord(substr($kalimat, $i, 1)));
        while (strlen($temp) < 8) {
            $temp = '0' . $temp;
        }
        array_push($encoded, $temp);
    }
    return $encoded;
}

function countStr($block)
{
    while (strlen($block) < 32) {
        $block = '0' . $block;
    }
    return $block;
}

function decConverter($kalimat)
{
    $length  = strlen($kalimat);
    $encoded = array();
    for ($i = 0; $i < $length; $i++) {
        $temp = ord(substr($kalimat, $i, 1));
        array_push($encoded, $temp);
    }
    return $encoded;
}

function GenerateKey($key)
{
    $r  = 20;
    $w  = 32;
    $Pw = hexdec('B7E15163');
    $Qw = hexdec('9E3779B9');

    $modulo = pow(2, 32);

    $S = array();
    array_push($S, $Pw);

    for ($i = 1; $i < 44; $i++) {
        // inisialisasi
        $x = ($S[$i - 1] + $Qw) % $modulo;
        array_push($S, $x);
    }

    return $S;
}

function GenerateKeyEnc($key)
{
    $r  = 20;
    $w  = 32;
    $Pw = hexdec('B7E15163');
    $Qw = hexdec('9E3779B9');

    $modulo = pow(2, 32);

    $S = array();
    array_push($S, $Pw);

    for ($i = 1; $i < 44; $i++) {
        // inisialisasi
        $x = $S[$i - 1] + $Qw;
        array_push($S, $x);
    }

    // ubah userkey menjadi binner
    $c = bindec(max(blockConverter($key)));

    $iteration = 3 * max($c, 2 * $r + 4);
    $X         = $Y         = $a         = $b         = 0;

    // $L = array_fill(0, $iteration, 0);
    $L    = array();
    $L[0] = 0;

    for ($index = 0; $index < $iteration; $index++) {
        $decX  = decbin(($S[$a] + $X + $Y) % $modulo);
        $S[$a] = bindec(ROL($decX, 3));
        $X     = $S[$a];
        $decY  = decbin(($L[$b] + $X + $Y) % $modulo);
        // $L[$b] = array_push($L, bindec(ROL($decY, ($X + $Y) % 32)));
        array_push($L, bindec(ROL($decY, ($X + $Y) % 32)));
        $Y = $L[$b];
        $a = ($a + 1) % (2 * $r + 4);
        $b = ($b + 1) % $iteration;

    }

    return $S;
}
