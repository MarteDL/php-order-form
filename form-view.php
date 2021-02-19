<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" type="text/css"
          rel="stylesheet"/>
    <title>Order food & drinks</title>
</head>
<body>
<div class="container">
    <?php
    if(isset($_SESSION["message"])){
        echo $_SESSION["message"];
        unset($_SESSION["message"]);
    }
    ?>
    <h1>Order food in restaurant "the Personal Ham Processors"</h1>
    <nav>
        <ul class="nav">
            <li class="nav-item">
                <a class="nav-link active" href="?food=1">Order only food</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="?food=0">Order only drinks</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="?food=2">Order both</a>
            </li>
        </ul>
    </nav>
    <form method="post">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="email">E-mail:</label>
                <input type="text" id="email" name="email" value="<?php echo isset($_POST["email"]) ? $_POST["email"] : ''; ?>" class="form-control" />
                <span class="text-danger"><?php echo isset($errorArray["email"]) ? $errorArray["email"] : '' ?></span>
            </div>
            <div></div>
        </div>

        <fieldset>
            <legend>Address</legend>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="street">Street:</label>
                    <input type="text" name="street" id="street" value="<?php echo isset($_POST["street"]) ? $_POST["street"] : ''; ?>" class="form-control">
                    <span class="text-danger"><?php echo isset($errorArray["street"]) ? $errorArray["street"] : '' ?></span>
                </div>
                <div class="form-group col-md-6">
                    <label for="streetnumber">Street number:</label>
                    <input type="text" id="streetnumber" name="streetnumber" value="<?php echo isset($_POST["streetnumber"]) ? $_POST["streetnumber"] : ''; ?>" class="form-control">
                    <span class="text-danger"><?php echo isset($errorArray["streetnumber"]) ? $errorArray["streetnumber"] : '' ?></span>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="city">City:</label>
                    <input type="text" id="city" name="city" value="<?php echo isset($_POST["city"]) ? $_POST["city"] : ''; ?>" class="form-control">
                    <span class="text-danger"><?php echo isset($errorArray["city"]) ? $errorArray["city"] : '' ?></span>
                </div>
                <div class="form-group col-md-6">
                    <label for="zipcode">Zipcode</label>
                    <input type="text" id="zipcode" name="zipcode" value="<?php echo isset($_POST["zipcode"]) ? $_POST["zipcode"] : ''; ?>" class="form-control">
                    <span class="text-danger"><?php echo isset($errorArray["zipcode"]) ? $errorArray["zipcode"] : '' ?></span>
                </div>
            </div>
        </fieldset>

        <fieldset>
            <legend>Products</legend><?php foreach ($products->getProducts() as $i => $product): ?>
                <label>
                    <input type="number" value="0" min="0" max="10"
                           name="products[<?php echo $i ?>]"/> <?php echo $product->getName() ?> -
                    &euro; <?php echo number_format($product->getPrice(), 2) ?></label><br/>
            <?php endforeach; ?>
        </fieldset>

        <label>
            <input type="checkbox" name="express_delivery" value="5" /> 
            Express delivery (+ 5 EUR) 
        </label>
            
        <button type="submit" class="btn btn-primary" name="submit">Order!</button>
    </form>

    <footer>You already ordered <strong>&euro; <?php echo isset($_COOKIE["totalValue"]) ? $_COOKIE["totalValue"] : '0' ?></strong> in food and drinks.</footer>
</div>

<style>
    footer {
        text-align: center;
    }

</style>
</body>
</html>
