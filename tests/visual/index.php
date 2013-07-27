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
    </body>
</html>

<?php
$ob = ob_get_clean();

require_once("../../vendor/autoload.php");
$spark = new Spark\Core\Spark();
$spark->render($ob);
?>