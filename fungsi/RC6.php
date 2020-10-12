<?php

// class RC6
// {

// function index($plaintext, $key)
// {

//     echo '<div class="col-md-6">';
// }

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
    $b = 16;
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

// fungsi untuk encripsi dengan input kalimat sebagai plaintext dan S adalah array dari kunci
function encrypt($sentence, $s)
{

    // waktu mulai
    $time_start = microtime(true);

    // mengkonvert kalimat menjadi 4 register / block dengan panjang 32 bit
    $encode       = blockConverter(str_pad($sentence, 16));
    $encodeLenght = count($encode);

    // mengkonvert setiap register atau block dari binner menjadi desimal
    $A = bindec($encode[0]);
    $B = bindec($encode[1]);
    $C = bindec($encode[2]);
    $D = bindec($encode[3]);

    // inisialiasi array untuk mengembalikan data
    $cipher = array();

    // inisialisasi variabel
    $r      = 20;
    $w      = 32;
    $logw   = 5;
    $modulo = pow(2, $w);

    // instansi variabel B dan D
    $B = ($B + $s[0]) % $modulo;
    $D = ($D + $s[1]) % $modulo;

    // looping untuk prosessing ecnripsi
    for ($i = 1; $i < $r + 1; $i++) {

        // mengalikan D dengan 2
        $twoMultipleD = gmp_mul("2", $D);
        // menambahkan $twoMultipleD dengan 1
        $twoMultipleDPlusOne = gmp_add($twoMultipleD, "1");
        // mengalikan variabel $twoMultipleDPlusOne dengan D
        $DMultipleTwoMultipleDPlusOne = gmp_mul($D, $twoMultipleDPlusOne);

        // mengalikan B dengan 2
        $twoMultipleB = gmp_mul("2", $B);
        // menambahkan $twoMultipleB dengan 1
        $twoMultipleBPlusOne = gmp_add($twoMultipleB, "1");
        // mengalikan variabel $twoMultipleBPlusOne dengan D
        $BMultipleTwoMultipleBPlusOne = gmp_mul($B, $twoMultipleBPlusOne);

        // $DMultipleTwoMultipleDPlusOne di mod dengan $modulo
        $u_temp = gmp_mod($DMultipleTwoMultipleDPlusOne, $modulo);
        // shift ke kiri dari $t_temp sebanyak $logw dengan panjang 32 bit
        $u = ROL($u_temp, $logw, 32);

        // BDMultipleTwoMultipleBPlusOne di mod dengan $modulo
        $t_temp = gmp_mod($BMultipleTwoMultipleBPlusOne, $modulo);
        // shift ke kiri dari $u_temp sebanyak $logw dengan panjang 32 bit
        $t = ROL($t_temp, $logw, 32);

        // $t dimod dengan 32
        $tmod = gmp_mod($t, 32);
        // $u dimod dengan 32
        $umod = gmp_mod($u, 32);

        // baris biner A di XOR dengan T
        $AXorT = gmp_xor($A, $t);
        // geser ke kiri dari $AXor sebanyak $umod dengan panjang 32 bit
        $ROLA = ROL($AXorT, $umod, 32);
        // menambahkan ROLA dengan $s
        $ROLAPlusS = gmp_add($ROLA, $s[2 * $i]);
        // $ROLAPlusS di mod dengan $modulo
        $A = gmp_mod($ROLAPlusS, $modulo);

        // baris biner C di XOR dengan U
        $CXorU = gmp_xor($C, $u);
        // geser ke kiri dari $CXor sebanyak $tmod dengan panjang 32 bit
        $ROLC = ROL($CXorU, $tmod, 32);
        // menambahkan ROLC dengan $s
        $ROLCPlusS = gmp_add($ROLC, $s[2 * $i + 1]);
        // $ROLCPlusS di mod dengan $modulo
        $C = gmp_mod($ROLCPlusS, $modulo);

        // merotasi nilai dari blok B -> A, C -> B, D -> C, A -> D
        $temp = $A;
        $A    = $B;
        $B    = $C;
        $C    = $D;
        $D    = $temp;

    }

    // menambahkan $A dengan $s dan kemudia di mod dengan $modulo
    $A = ($A + $s[2 * $r + 2]) % $modulo;
    // menambahkan $C dengan $s dan kemudia di mod dengan $modulo
    $C = ($C + $s[2 * $r + 3]) % $modulo;

    // waktu selsai
    $time_end = microtime(true);

    // menambahkan 4 register atau block kedalam array
    array_push($cipher, $A, $B, $C, $D);

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

    // mengkonvert ciphertext mejadi 4 blok dengan panjang 32 bit
    $encode = blockConverter($sentence);
    // menghitung panjang dari $encode
    $encodeLenght = count($encode);

    // mengkonvert setiap register atau block dari binner menjadi desimal
    $A = bindec($encode[0]);
    $B = bindec($encode[1]);
    $C = bindec($encode[2]);
    $D = bindec($encode[3]);

    // inisialiasi array untuk mengembalikan data
    $original = array();

    // inisialisasi variabel
    $r      = 20;
    $w      = 32;
    $logw   = 5;
    $modulo = pow(2, $w);

    // instansi variabel C dan A
    $C = ($C - $s[2 * $r + 3]) % $modulo;
    $A = ($A - $s[2 * $r + 2]) % $modulo;

    // looping untuk prosessing decryption
    for ($i = $r; $i >= 1; $i--) {

        // merotasi nilai dari blok C -> D, B -> C, A -> B, D -> A
        $temp = $D;
        $D    = $C;
        $C    = $B;
        $B    = $A;
        $A    = $temp;

        // mengalikan D dengan 2
        $twoMultipleD = gmp_mul("2", $D);
        // menambahkan $twoMultipleD dengan 1
        $twoMultipleDPlusOne = gmp_add($twoMultipleD, "1");
        // mengalikan variabel $twoMultipleDPlusOne dengan D
        $DMultipleTwoMultipleDPlusOne = gmp_mul($D, $twoMultipleDPlusOne);

        // mengalikan B dengan 2
        $twoMultipleB = gmp_mul("2", $B);
        // menambahkan $twoMultipleB dengan 1
        $twoMultipleBPlusOne = gmp_add($twoMultipleB, "1");
        // mengalikan variabel $twoMultipleBPlusOne dengan D
        $BMultipleTwoMultipleBPlusOne = gmp_mul($B, $twoMultipleBPlusOne);

        // BDMultipleTwoMultipleBPlusOne di mod dengan $modulo
        $t_temp = gmp_mod($BMultipleTwoMultipleBPlusOne, $modulo);
        // shift ke kiri dari $u_temp sebanyak $logw dengan panjang 32 bit
        $t = ROL($t_temp, $logw, 32);

        // $DMultipleTwoMultipleDPlusOne di mod dengan $modulo
        $u_temp = gmp_mod($DMultipleTwoMultipleDPlusOne, $modulo);
        // shift ke kiri dari $t_temp sebanyak $logw dengan panjang 32 bit
        $u = ROL($u_temp, $logw, 32);

        // $t dimod dengan 32
        $tmod = gmp_mod($t, 32);
        // $u dimod dengan 32
        $umod = gmp_mod($u, 32);

        // $C dikurangi dengan $s
        $CMinusS = gmp_sub($C, $s[2 * $i + 1]);
        // $CMinus di mod kan dengan $modulo
        $CMinusSModByModulo = gmp_mod($CMinusS, $modulo);
        // $CMinusSModByModulo di geser ke kanan sebanyak $tmod dengan panjang 32 bit
        $RORCMinusSModByModuloAndTMod = ROR($CMinusSModByModulo, $tmod, 32);
        // $RORCMinusSModByModuloAndTMod di xor kan dengan $u
        $C = gmp_xor($RORCMinusSModByModuloAndTMod, $u);

        // $A dikurangi dengan $s
        $AMinusS = gmp_sub($A, $s[2 * $i]);
        // $AMinus di mod kan dengan $modulo
        $AMinusSModByModulo = gmp_mod($AMinusS, $modulo);
        // $AMinusSModByModulo di geser ke kanan sebanyak $tmod dengan panjang 32 bit
        $RORAMinusSModByModuloAndUMod = ROR($AMinusSModByModulo, $umod, 32);
        // $RORAMinusSModByModuloAndTMod di xor kan dengan $u
        $A = gmp_xor($RORAMinusSModByModuloAndUMod, $t);

    }

    // $D dikurangin dengan $s dan kemudian di mod kan dengan $modulo
    $D = ($D - $s[1]) % $modulo;
    // $B dikurangin dengan $s dan kemudian di mod kan dengan $modulo
    $B = ($B - $s[0]) % $modulo;

    // menambahka 4 block ke dalam array $original
    array_push($original, $A, $B, $C, $D);

    // waktu selsai
    $time_end = microtime(true);

    // menghitung waktu eksekusi
    $execution_time = ($time_end - $time_start) . ' second';

    // mengembalikan nilai value dari $original
    // return $original;
    return array('original' => $original, 'time' => $execution_time);
}
// }
