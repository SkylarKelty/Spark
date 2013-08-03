<?php
$t = microtime(true);
require_once("src/loader.php");
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

        <div class="version">
            <SparkVersion />
        </div>

        <div class="time">
            <?php
            require_once("src/run.php");
            print "\n" . '<p>Page loaded in ' . round((microtime(true) - $t), 3) . ' seconds</p>';
            ?>
        </div>
    </body>
</html>