<?php
require_once("../../vendor/autoload.php");
$spark = new Spark\Core\Spark();

// This is a Boolean switch
$spark->addTag("Switch", function($html, $inner) {
    if ($inner == "True") return "False";
    if ($inner == "False") return "True";
    return "Error: " . htmlentities($html);
});

// This is a Boolean 'Or' Operator
$spark->addTag("Logic", function($html, $inner) {
    $inner = str_replace(array("\n", " "), "", $inner);
    if ($inner == "TrueFalse") return "True";
    if ($inner == "FalseTrue") return "True";
    if ($inner == "TrueTrue") return "True";
    return "False";
});

// This is a simple Passthrough
$spark->addTag("Pass", function($html, $inner) {
    return $inner;
});