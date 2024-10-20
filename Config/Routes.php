<?php

use CodeIgniter\Router\RouteCollection;


/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

//API routes

$routes->group("product", ["namespace"=>"App\Controllers\Api", "filter" => "basic_auth"], function($routes) {
    $routes->post("add", "ProductController::addProduct");
    //get all products
    $routes->get("list", "ProductController::listAllProducts");
    //get one product
    $routes->get("(:num)", "ProductController::getSingleProduct/$1");
    //update product
    $routes->put("(:num)", "ProductController::updateProduct/$1");
    //delete product
    $routes->delete("(:num)", "ProductController::deleteProduct/$1");
    

});
