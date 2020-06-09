<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

define('TIMEZONE', 'Europe/Bucharest');
date_default_timezone_set(TIMEZONE);


ini_set('display_errors', true);
error_reporting(E_ALL | E_STRICT);

require_once 'Router.php';
require_once 'config/database.php';
require_once 'model/Product.php';
require_once 'controller/ProductController.php';
require_once 'model/User.php';
require_once 'controller/UserController.php';
require_once 'model/Auth_Token.php';

$db = new Database();
$connection = $db->connection();



$userModel = new User($connection);
$authModel = new Auth_Token($connection);
$userController = new UserController($userModel, $authModel);

$productModel = new Product($connection);
$productController = new ProductController($productModel, $authModel);


Router::route('login', function () use ($userController) {
    return $userController->login();
});

Router::route('products/read', function () use ($productController) {
    return $productController->read();
});

Router::route('products/create', function () use ($productController) {
    return $productController->create();
});

Router::route('products/update', function () use ($productController) {
    return $productController->update();
});

Router::route('products/delete', function () use ($productController) {
    return $productController->delete();
});



Router::execute($_SERVER['REQUEST_URI']);
