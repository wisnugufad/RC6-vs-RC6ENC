<?php

// fungsi untuk shift biner dari $x ke kanan sebanyak $n kali dengan panjang 32bit
function ROR($x, $n, $bits = 32)
{
    // operasi eksponensial 2 dengan $ x dan hasil minus 1
    $mask = pow(2, $n) - 1;

    //  $x bitwise XOR dengan $mask
    $mask_bits = $x & $mask;

    // mengembalikan nilai yang sudah dishift
    return ($x >> $n) | ($mask_bits << ($bits - $n));
}

// fungsi untuk shift biner dari $x ke kanan sebanyak $n kali dengan panjang 32bit
function ROL($x, $n, $bits = 32)
{
    // memanggil fungsi ROR dengan paramater negatif $n
    return ROR($x, $bits - $n, $bits);
}

// fungsi untuk membuat 4 block dengan panjang 32bit disetiap block
function blockConverter($sentence)
{
    //inisialisasi variabel
    $encode = array();
    $res    = null;

    // looping untuk mengganti nilai string jadi nilai biner
    for ($i = 0; $i < strlen($sentence); $i++) {

        // jika counter modulo dengan 4 adalah 0, maka semua biner dimasukin ke $encode
        if ($i % 4 == 0 && $i != 0) {

            array_push($encode, $res);
            $res = "";
        }

        // mengganti data tipe dari char ke desimal
        $charToDecimal = ord($sentence[$i]);

        // mengganti tipe data desimal ke biner
        $decimalToBinary = decbin($charToDecimal);

        // jika sebuah biner panjangnya kurang dari 8 maka menambahkan 0 didepan biner tersebut
        if (strlen($decimalToBinary) < 8) {

            $eightBits = str_pad($decimalToBinary, 8, 0, STR_PAD_LEFT);

        } else {

            $eightBits = $decimalToBinary;
        }

        // menambahkan setiap biner ke dalma variabel
        $res = $res . $eightBits;
    }

    // menambahkan semua variabel kedalam array
    array_push($encode, $res);

    // return value dari 4 block
    return $encode;
}

// fungsi untuk mengembalikan 4 blok kedalam char
function reverseBlockConverter($block)
{
    // inisialisasi variabel
    $sentence = "";

    // looping untuk mengganti inputan desimal ke dalam binner
    for ($i = 0; $i < count($block); $i++) {

        // mengganti tipe data desimal kedalam biner
        $decimalToBinary = decbin($block[$i]);

        // jika biner panjangnya kurang dari 32 bit, maka tambahkan 0 di depan biner
        if (strlen($decimalToBinary) < 32) {

            $thirtyTwoBits = str_pad($decimalToBinary, 32, 0, STR_PAD_LEFT);

        } else {

            $thirtyTwoBits = $decimalToBinary;
        }

        // looping untuk mengganti nilai 32 bit dari biner ke dalam char
        for ($j = 0; $j < 4; $j++) {

            // mengambil 8 binner pertama
            $getSpesifikBinary = substr($thirtyTwoBits, $j * 8, 8);

            // mengganti tipe data biner menjadi desimal
            $binerToDecimal = bindec($getSpesifikBinary);

            // mengganti tipe data desimal kedalam char
            $decimalToChar = chr($binerToDecimal);

            // menambahkan setiap char ke dalam variabel
            $sentence .= $decimalToChar;
        }
    }

    // mengembalikan nilai value dari char
    return $sentence;
}

// fungsi untuk membuat key dari inputan user
function keySchedule($userKey)
{
    // inisialisasi variabel
    $r      = 20;
    $w      = 32;
    $modulo = pow(2, $w);

    // loopoing untuk inisiasi variabel $s dengan nilai default 0
    for ($i = 0; $i < (2 * $r + 4); $i++) {

        $s[$i] = 0;
    }

    // inisialisasi dari $s dengan index 0 dengan default value
    $s[0] = 0xB7E15163;

    // looping untuk memberikan nilai dari sebuah array setelah index ke 0
    for ($i = 1; $i < (2 * $r + 4); $i++) {

        $s[$i] = ($s[$i - 1] + 0x9E3779B9) % (2 ** $w);
    }

    // memanggil fungsi blockConverter untuk masukan key dari user
    $encode = blockConverter(str_pad($userKey, 16));

    // menghitung arrar dari $encode
    $encodeLenght = count($encode);

    // looping membuat inisiasi dari array $l dengan nilai default 0 sebanyak $encodeLenght
    for ($i = 0; $i < $encodeLenght; $i++) {

        $l[$i] = 0;
    }

    // menambahkan nilai dari $encode ke dalam array $l
    for ($i = 1; $i < $encodeLenght + 1; $i++) {

        $l[$encodeLenght - $i] = bindec($encode[$i - 1]);
    }

    // menghitung nilai maximum dari $encodeLenght dan penjumlahan dari iterasi
    // $v = max($encodeLenght, 2 * $r + 4);

    // jika menggunakan nilai C sebagai max
    $w = strlen($userKey);
    $u = $w / 8;
    $b = 32;
    $c = ceil( $b / $u );
    $v = max($c, 2 * $r + 4);

    // inisialisasi variabel
    $A = 0;
    $B = 0;
    $i = 0;
    $j = 0;

    // menambahkan value untuk membuat key dalam array $s
    for ($index = 0; $index < $v; $index++) {

        $A = $s[$i] = ROL(($s[$i] + $A + $B) % $modulo, 3, 32);
        $B = $l[$j] = ROL(($l[$j] + $A + $B) % $modulo, ($A + $B) % 32, 32);
        $i = ($i + 1) % (2 * $r + 4);
        $j = ($j + 1) % $encodeLenght;
    }

    // mengembalikan nilai $s
    return $s;
}

// fungsi untuk membuat F
function makeF($block1, $block2)
{

    $modulo = pow(2, 32);
    // $powerBlock1    = pow(2, $block1);
    $powerBlock1 = gmp_pow($block1, "2");

    // $powerBlock1Mod = $powerBlock1 % $modulo;
    // $powerBlock1Mod = gmp_mod($powerBlock1, $modulo);

    // $powerBlock2    = pow(2, $block2);
    $powerBlock2 = gmp_pow($block2, "2");

    // $powerBlock2Mod = $powerBlock2 % $modulo;
    // $powerBlock2Mod = gmp_mod($powerBlock2, $modulo);

    // $block1MultipleBlock2    = $block1 * $block2;
    $block1MultipleBlock2 = gmp_mul($block1, $block2);
    // $block1MultipleBlock2Mod = $block1MultipleBlock2 % $modulo;
    // $block1MultipleBlock2Mod = gmp_mod($block1MultipleBlock2, $modulo);

    // $powerB1plusB2 = $powerBlock1Mod + $powerBlock2Mod;
    $powerB1plusB2 = gmp_add($powerBlock1, $powerBlock2);
    $absoluteBlock = gmp_sub($powerB1plusB2, $block1MultipleBlock2);

    // $result = abs($powerB1plusB2 - $block1MultipleBlock2Mod) - 7;
    $absoluteBlockMinusSeven = gmp_sub($absoluteBlock, "7");

    $result = gmp_mod($absoluteBlockMinusSeven, $modulo);

    return $result;
}

// fungsi untuk encripsi dengan input kalimat sebagai plaintext dan S adalah array dari kunci
function encrypt($sentence, $s)
{
    // waktu mulai
    $time_start = microtime(true);

    // mengkonvert kalimat menjadi 8 register / block dengan panjang 32 bit
    $encode = blockConverter(str_pad($sentence, 32));
    // $encode       = blockConverter($sentence);
    $encodeLenght = count($encode);

    // inisialisasi variabel
    $r      = 20;
    $w      = 32;
    $modulo = pow(2, $w);

    // mengkonvert setiap register atau block dari binner menjadi desimal
    $A = bindec($encode[0]);
    $B = bindec($encode[1]);
    $C = bindec($encode[2]);
    $D = bindec($encode[3]);
    $E = bindec($encode[4]);
    $F = bindec($encode[5]);
    $G = bindec($encode[6]);
    $H = bindec($encode[7]);

    // inisialiasi array untuk mengembalikan data
    $cipher = array();

    $bitB = $B + $s[0];
    $B    = gmp_mod($bitB, $modulo);
    $D    = gmp_xor($D, $s[0]);
    $bitF = $F + $s[1];
    $F    = gmp_mod($bitF, $modulo);
    $H    = gmp_xor($H, $s[1]);

    for ($i = 1; $i <= $r; $i++) {

        $F1 = makeF($B, $F);
        $F2 = makeF($D, $H);

        $F1mod = gmp_mod($F1, 32);
        $F2mod = gmp_mod($F2, 32);

        $R1 = ROL($F1, $F2mod, 32);
        $R2 = ROL($F2, $F1mod, 32);

        $R1mod = gmp_mod($R1, 32);
        $R2mod = gmp_mod($R2, 32);

        $xorA         = gmp_xor($A, $R1);
        $RolXorA      = ROL($xorA, $R2mod);
        $RolXorAplusS = gmp_add($RolXorA, $s[2 * $i]);
        $A            = gmp_mod($RolXorAplusS, $modulo);

        $CplusR       = gmp_add($C, $R2);
        $CplusRmod    = gmp_mod($CplusR, $modulo);
        $RolCplusRmod = ROL($CplusRmod, $R1mod);
        $C            = gmp_xor($RolCplusRmod, $s[2 * $i]);

        $xorE         = gmp_xor($E, $R1);
        $RolXorE      = ROL($xorE, $R2mod);
        $RolXorEplusS = gmp_add($RolXorE, $s[2 * $i + 1]);
        $E            = gmp_mod($RolXorEplusS, $modulo);

        $GplusR       = gmp_add($G, $R2);
        $GplusRmod    = gmp_mod($GplusR, $modulo);
        $RolGplusRmod = ROL($GplusRmod, $R1mod);
        $G            = gmp_xor($RolGplusRmod, $s[2 * $i + 1]);

        $temp = $A;
        $A    = $B;
        $B    = $C;
        $C    = $D;
        $D    = $E;
        $E    = $F;
        $F    = $G;
        $G    = $H;
        $H    = $temp;
    }

    $AplusS = gmp_add($A, $s[42]);
    $A      = gmp_mod($AplusS, $modulo);

    $C = gmp_xor($C, $s[42]);

    $EplusS = gmp_add($E, $s[43]);
    $E      = gmp_mod($EplusS, $modulo);

    $G = gmp_xor($G, $s[43]);

    // menambahkan 4 register atau block kedalam array
    array_push($cipher, $A, $B, $C, $D, $E, $F, $G, $H);

    // waktu selsai
    $time_end = microtime(true);

    // menghitung waktu eksekusi
    $execution_time = ($time_end - $time_start) . ' second';

    // mengem balikan value dari hasil encriksi dan waktu
    return array('chipper' => $cipher, 'time' => $execution_time);

}

// fungsi untuk dekripsi dengan input kalimat sebagai plaintext dan S adalah array dari kunci
function decrypt($sentence, $s)
{

    // waktu mulai
    $time_start = microtime(true);

    // mengkonvert ciphertext mejadi 8 blok dengan panjang 32 bit
    $encode = blockConverter($sentence);
    // $encode = $sentence;
    // menghitung panjang dari $encode
    $encodeLenght = count($encode);

    // mengkonvert setiap register atau block dari binner menjadi desimal
    $A = bindec($encode[0]);
    $B = bindec($encode[1]);
    $C = bindec($encode[2]);
    $D = bindec($encode[3]);
    $E = bindec($encode[4]);
    $F = bindec($encode[5]);
    $G = bindec($encode[6]);
    $H = bindec($encode[7]);

    // inisialisasi variabel
    $r             = 20;
    $w             = 32;
    $modulo        = pow(2, $w);
    $negatifModulo = pow(-2, $w);

    // inisialiasi array untuk mengembalikan data
    $original = array();

    $C       = gmp_xor($C, $s[42]);
    $AminusS = gmp_sub($A, $s[42]);
    $A       = gmp_mod($AminusS, $modulo);

    $G       = gmp_xor($G, $s[43]);
    $EminusS = gmp_sub($E, $s[43]);
    $E       = gmp_mod($EminusS, $modulo);

    for ($i = 20; $i >= 1; $i--) {

        $temp = $H;
        $H    = $G;
        $G    = $F;
        $F    = $E;
        $E    = $D;
        $D    = $C;
        $C    = $B;
        $B    = $A;
        $A    = $temp;

        $F1 = makeF($B, $F);
        $F2 = makeF($D, $H);

        $F1mod = gmp_mod($F1, 32);
        $F2mod = gmp_mod($F2, 32);

        $R1 = ROL($F1, $F2mod, 32);
        $R2 = ROL($F2, $F1mod, 32);

        $R1mod = gmp_mod($R1, 32);
        $R2mod = gmp_mod($R2, 32);

        $AminusS       = gmp_sub($A, $s[2 * $i]);
        $AminusSmod    = gmp_mod($AminusS, $modulo);
        $RORAminusSmod = ROR($AminusSmod, $R2mod, 32);
        $A             = gmp_xor($RORAminusSmod, $R1);

        $xorC           = gmp_xor($C, $s[2 * $i]);
        $RORxorC        = ROR($xorC, $R1mod, 32);
        $RORxorCminusR2 = gmp_sub($RORxorC, $R2);
        $C              = gmp_mod($RORxorCminusR2, $modulo);

        $EminusS       = gmp_sub($E, $s[2 * $i + 1]);
        $EminusSmod    = gmp_mod($EminusS, $modulo);
        $ROREminusSmod = ROR($EminusSmod, $R2mod, 32);
        $E             = gmp_xor($ROREminusSmod, $R1);

        $xorG           = gmp_xor($G, $s[2 * $i + 1]);
        $RORxorG        = ROR($xorG, $R1mod, 32);
        $RORxorGminusR2 = gmp_sub($RORxorG, $R2);
        $G              = gmp_mod($RORxorGminusR2, $modulo);

    }

    $D       = gmp_xor($D, $s[0]);
    $BminusS = gmp_sub($B, $s[0]);
    $B       = gmp_mod($BminusS, $modulo);
    $H       = gmp_xor($H, $s[1]);
    $FminusS = gmp_sub($F, $s[1]);
    $F       = gmp_mod($FminusS, $modulo);

    // menambahka 4 block ke dalam array $original
    array_push($original, abs($A), $B, $C, $D, $E, $F, $G, $H);

    // waktu selsai
    $time_end = microtime(true);

    // menghitung waktu eksekusi
    $execution_time = ($time_end - $time_start) . ' second';

    // mengem balikan value dari hasil encriksi dan waktu
    return array('original' => $original, 'time' => $execution_time);
}

// }
