<?php

//this line makes PHP behave in a more strict way
declare(strict_types=1);

use JetBrains\PhpStorm\NoReturn;
use JetBrains\PhpStorm\Pure;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
set_error_handler("var_dump");

//we are going to use session variables so we need to enable sessions
session_start();

function whatIsHappening()
{
    echo '<h2>$_GET</h2>';
    var_dump($_GET);
    echo '<h2>$_POST</h2>';
    var_dump($_POST);
    echo '<h2>$_COOKIE</h2>';
    var_dump($_COOKIE);
    echo '<h2>$_SESSION</h2>';
    var_dump($_SESSION);
}

function displaySessions()
{
    $_POST["street"] = $_SESSION["street"];
    $_POST["streetnumber"] = $_SESSION["streetnumber"];
    $_POST["city"] = $_SESSION["city"];
    $_POST["zipcode"] = $_SESSION["zipcode"];
}

function setSessionVariables()
{
    $_SESSION["street"] = $_POST["street"];
    $_SESSION["streetnumber"] = $_POST["streetnumber"];
    $_SESSION["city"] = $_POST["city"];
    $_SESSION["zipcode"] = $_POST["zipcode"];
}

function validateForm(&$email_error, &$street_error, &$streetnumber_error, &$city_error, &$zipcode_error): bool
{
    if (empty($_POST["email"])) {
        $email_error = "* E-mail is a required field";
    } else if (filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) == false) {
        $_POST["email"] = "";
        $email_error = "* Your e-mail address is not valid";
    }

    if (empty($_POST["street"])) {
        $street_error = "* Street is a required field";
    }

    if (empty($_POST["streetnumber"])) {
        $streetnumber_error = "* Streetnumber is a required field";
    } else if (!is_numeric($_POST["streetnumber"])) {
        $_POST["streetnumber"] = "";
        $streetnumber_error = "* Your streetnumber is not a number";
    }

    if (empty($_POST["city"])) {
        $city_error = "* City is a required field";
    }

    if (empty($_POST["zipcode"])) {
        $zipcode_error = "* Zipcode is a required field";
    } else if (!is_numeric($_POST["zipcode"])) {
        $_POST["zipcode"] = "";
        $zipcode_error = "* Your zipcode is not a number";
    }

    if (!empty($_POST["email"]) && !empty($_POST["street"]) && !empty($_POST["streetnumber"]) && !empty($_POST["city"]) && !empty($_POST["zipcode"])) {
        return true;
    } else {
        return false;
    }
}

function getTotalValue(): string
{
    if (isset($_COOKIE["totalValue"])) {
        return $_COOKIE["totalValue"];
    } else {
        return '0';
    }
}

function displayBoughtItems(object $products, string $totalValue): string
{
    $priceOfThisOrder = 0;
    if (isset($_POST["express_delivery"])) {
        $priceOfThisOrder += 5;
    }

//    $mailMessage = "Thank you for your order at 'the Personal Ham Processors! (\r\n)You ordered: ";
    $alertMessage = '<div class="alert alert-success" role="alert"><p>Your form has been sent! Thank you for your order.</p>Your order: ';
    $deliveryTime = calculateDeliveryTime();
    foreach ($products->getProducts() as $i => $product) {
        if (!empty($_POST["products"][$i])) {
            $priceOfThisOrder += ($product->getPrice() * ($_POST["products"][$i]));
            $alertMessage .= "<li>" . $_POST["products"][$i] . "x " . $product->getName() . "</li>";
//            $mailMessage .= $_POST["products"][$i] . "x " . $product["name"] . "(\r\n)";
            unset($_POST["product"]);
        }
    }

//    $headers = "MIME-Version: 1.0" . "\r\n";
//    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
//    $headers .= "FROM: martedeleeuw@hotmail.com". "\r\n";
//    $mailMessage .= "The total cost of this order is " . $priceOfThisOrder . " EURO and it will be delivered at your house at ". $deliveryTime.".";
//    mail($_POST["email"], "Confirmation mail", $mailMessage, $headers);

    setcookie("totalValue", strval(floatval($totalValue) + $priceOfThisOrder), time() + 60 * 60 * 24 * 30);
    return $alertMessage . "</br><p>The total cost of this order is &euro;" . $priceOfThisOrder . " and it will be delivered at your house at " . $deliveryTime . ".</p></ul></div>";
}

#[Pure] function calculateDeliveryTime(): string
{
    if (isset($_POST["express_delivery"])) {
        $deliveryTime = date('H:i', strtotime("+ 45 minutes"));
    } else {
        $deliveryTime = date('H:i', strtotime("+2 hours"));
    }
    return $deliveryTime;
}

#[NoReturn] function submitOrder($products, $totalValue) : void
{
    $_SESSION["message"] = displayBoughtItems($products, $totalValue);
    unset($_POST["email"]);
    $host = $_SERVER['HTTP_HOST'];
    $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $extra = 'index.php?food=1';

    header("Location: http://$host$uri/$extra");
    exit;
}

function getProductList(): object
{
//    $page = basename($_SERVER['REQUEST_URI']);

    if ($_GET["food"] == 0) {
        return new ProductList([
            new Product('Cola', 2),
            new Product('Fanta', 2),
            new Product('Sprite', 2),
            new Product('Ice-tea', 3),
        ]);

    } else if ($_GET["food"] == 2) {
        return new ProductList([
            new Product('Club Cheese', 3),
            new Product('Club Cheese & Ham', 4),
            new Product('Club Chicken', 4),
            new Product('Club Salmon', 5),
            new Product('Cola', 2),
            new Product('Fanta', 2),
            new Product('Sprite', 2),
            new Product('Ice-tea', 3),
        ]);
    } else {
        return new ProductList([
            new Product('Club Ham', 3.20),
            new Product('Club Cheese', 3),
            new Product('Club Cheese & Ham', 4),
            new Product('Club Chicken', 4),
            new Product('Club Salmon', 5)
        ]);
    }
}

class Product
{
    private string $name;
    private float $price;

    public function __construct(string $name, float $price)
    {
        $this->name = $name;
        $this->price = $price;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }
}

class ProductList {

    private array $products;

    public function __construct(array $products)
    {
        $this->products = $products;
    }

    public function getProducts(): array
    {
        return $this->products;
    }
}

$products = getProductList();

$totalValue = getTotalValue();

$email_error = $street_error = $streetnumber_error = $city_error = $zipcode_error = "";

if (isset($_POST["submit"])) {
    setSessionVariables();
    if (validateForm($email_error, $street_error, $streetnumber_error, $city_error, $zipcode_error)) {
        submitOrder($products, $totalValue);
    }
} else if(isset($_SESSION["street"], $_SESSION["streetnumber"], $_SESSION["city"], $_SESSION["zipcode"])) {
    displaySessions();
}

require 'form-view.php';