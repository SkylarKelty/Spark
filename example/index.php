<?php
$t = microtime(true);
ob_start();
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Home | Spark</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="style/main.css" />
    </head>
    <body>
        <div class="container">
            <h1>Welcome to Spark!</h1>
            <h3>Running Tests...</h3>
            <p>Non-Nested boolean switch result: <SparkSwitch>False</SparkSwitch></p>
            <p>Nested boolean switch result: <SparkSwitch>
                <SparkSwitch>True</SparkSwitch>
            </SparkSwitch></p>
            <p>Multi-Nested boolean switch result: <SparkSwitch>
                    <SparkSwitch>
                        <SparkSwitch>False</SparkSwitch>
                    </SparkSwitch>
            </SparkSwitch></p>
            <p>Nested boolean logic result: <SparkLogic>
                    <SparkPass>
                        True
                    </SparkPass>
                    <SparkPass>
                        False
                    </SparkPass>
            </SparkLogic></p>
        </div>
        <div class="version"><SparkVersion /></div>

<?php
$ob = ob_get_clean();

require_once("../vendor/autoload.php");
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

// Render out
print $spark->run($ob);

print "\n" . '<p class="time">Page load in ' . round((microtime(true) - $t), 3) . ' seconds</p>';
?>
    </body>
</html>