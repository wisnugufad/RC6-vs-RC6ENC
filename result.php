<?php

// include('fungsi/Fungsi.php');

if (isset($_POST['submit'])) {
    $S         = array();
    $plaintext = $_POST['plaintext'];
    $key       = $_POST['key'];
    $alg       = $_POST['alg'];
    if ($alg == 'rc6') {
        include 'fungsi/RC6.php';
        $S                 = keySchedule($key);
        $enc               = encrypt($plaintext, $S);
        $chiper            = $enc['chipper'];
        $enc_time          = $enc['time'];
        $hasilReverseBlock = reverseBlockConverter($chiper);
        $dec               = decrypt(str_pad($hasilReverseBlock, 16), $S);
        $plaintextDec      = $dec['original'];
        $dec_time          = $dec['time'];
        $hasilDecrypt      = reverseBlockConverter($plaintextDec);
    } else {
        include 'fungsi/RC6ENC.php';
        $S                 = keySchedule($key);
        $enc               = encrypt(str_pad($plaintext, 32), $S);
        $chiper            = $enc['chipper'];
        $enc_time          = $enc['time'];
        $hasilReverseBlock = reverseBlockConverter($chiper);
        $dec               = decrypt(str_pad($hasilReverseBlock, 32), $S);
        $plaintextDec      = $dec['original'];
        $dec_time          = $dec['time'];
        $hasilDecrypt      = reverseBlockConverter($plaintextDec);
    }

} else {
    header('Location: index.php');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RC6 ENHANCE</title>
    <!-- CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <style>
        body{
            margin: 20px !important;
        }
    </style>
</head>
<body>
<div class="col-md-12">
    <br>
    <h1 align="center"><?php echo strtoupper($alg); ?></h1>
    <hr>
</div>
<div class="row">
    <div class="col-md-6">
        <h3>Plaintext = <?php echo $plaintext; ?></h3>
        <h3>Encripsi = <?php echo $hasilReverseBlock; ?></h3>
        <h3>Execution Time = <?php echo $enc_time; ?></h3>
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">index</th>
                    <th scope="col">value</th>
                    <th scope="col">binner</th>
                    <!-- <th scope="col">length</th> -->
                </tr>
            </thead>
            <tbody>
                <?php
foreach ($chiper as $k => $value) {
    echo '<tr>';
    echo '<th scope="row">' . $k . '</th>';
    echo '<td>' . $value . '</td>';
    echo '<td>' . $bin = decbin($value) . '</td>';
    // echo '<td>' . strlen($bin) . '</td>';
    echo '</tr>';
}
?>
            </tbody>
        </table>

    <hr>
    <h3>Dekripsi = <?php echo $hasilDecrypt; ?></h3>
    <h3>Execution Time = <?php echo $dec_time; ?></h3>
    <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">index</th>
                    <th scope="col">value</th>
                    <th scope="col">binner</th>
                    <!-- <th scope="col">length</th> -->
                </tr>
            </thead>
            <tbody>
                <?php
foreach ($plaintextDec as $p => $value2) {
    echo '<tr>';
    echo '<th scope="row">' . $p . '</th>';
    echo '<td>' . $value2 . '</td>';
    echo '<td>' . $bin2 = str_pad(decbin($value2), 32, 0, STR_PAD_LEFT) . '</td>';
    // echo '<td>' . strlen($bin2) . '</td>';
    echo '</tr>';
}
?>
            </tbody>
        </table>

    </div>

    <div class="col-md-6" style="background-color:#f0f0f0">
        <h3>Key = <?php echo $key; ?></h3>
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">index</th>
                    <th scope="col">value</th>
                    <th scope="col">binner</th>
                </tr>
            </thead>
            <tbody>
                <?php

foreach ($S as $k => $value) {
    echo '<tr>';
    echo '<th scope="row">' . $k . '</th>';
    echo '<td>' . $value . '</td>';
    echo '<td>' . decbin($value) . '</td>';
    echo '</tr>';
}
?>
            </tbody>
        </table>
    </div>
</div>

    <!-- JS -->
    <!-- <script src="js/bootstrap.js"></script>
    <script src="js/jquery-3.5.1.js"></script> -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>