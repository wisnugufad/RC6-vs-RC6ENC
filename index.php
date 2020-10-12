<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RC6 ENHANCE</title>
    <!-- CSS -->
    <!-- <link rel="stylesheet" href="css/bootstrap.css"> -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <style>
        body{
            margin: 20px !important;
        }
    </style>
</head>
<body>
    <h1>RC 6 ENHANCE</h1>

    <form action="result.php" method="post">
    <div class="form-group row">
    <label for="plaintext" class="col-sm-2 col-form-label">Plaintext</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" name="plaintext" id="plaintxt">
    </div>
    </div>

    <div class="form-group row">
    <label for="key" class="col-sm-2 col-form-label">Key</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" name="key" id="key">
    </div>
    </div>

    <div class="form-group row">
    <label for="alg" class="col-sm-2 col-form-label">Algoritma</label>
    <div class="col-sm-2">
        <select name="alg" id="alg" class="form-control">
            <option value="rc6">RC 6</option>
            <option value="rc6enc"> RC 6 ENC</option>
        </select>
        <span id='keterangan' style='color:red'>*Max length plaintext and key is 16</span>
    </div>
    </div>

    <button type="submit" name="submit" class="btn btn-block btn-primary">
        ENCRIP
    </button>
    </form>
    <!-- JS -->
    <!-- <script src="js/bootstrap.js"></script>
    <script src="js/jquery-3.5.1.js"></script> -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script >
    $(document).ready(function(){
        $('#alg').on('change', function (e) {
    var optionSelected = $("option:selected", this);
    if (this.value == 'rc6') {
        var d = "*Max length plaintext and key is 16";
    }else{
        var d = "*Max length plaintext and key is 32";
    }
    
    $('#keterangan').html(d);
});
    })
    </script>
</body>
</html>
