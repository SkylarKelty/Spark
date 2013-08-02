<?php
ob_start();
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Example</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <p>Hello world!</p>
        <SparkVersion></SparkVersion>
        <p>Nested boolean test result:
            <SparkTest>
                <SparkTest>
                    True
                </SparkTest>
            </SparkTest>
        </p>
    </body>
</html>

<?php
$ob = ob_get_clean();

require_once("../../vendor/autoload.php");
$spark = new Spark\Core\Spark();

// This is a Boolean switch
$spark->addTag("Test", function($html, $inner) {
    if ($inner == "True") return "False";
    if ($inner == "False") return "True";
    return "Error: " . htmlentities($html);
});

// Render out
$spark->render($ob);
?>