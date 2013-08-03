<?php
$t = microtime(true);
require_once("src/loader.php");
?>

<!DOCTYPE html>
<html>
    <head>
        <SparkHeader>Speed Test | Spark</SparkHeader>
    </head>
    <body>
        <div class="container">
            <h1>Speed Test</h1>
            <div class="tests">
                <?php
                for ($i = 0; $i <= 1000; $i++) {
                    print '<SparkPass></SparkPass>';
                }
                ?>
                <p>Parsed 1000 snippets in <STIME> seconds</p>
            </div>
        </div>
        <div class="version">
            <p>Rendered with <SparkVersion /></p>
        </div>
    </body>
</html>

<?php
global $spark;
$out = $spark->run(ob_get_clean());
print str_replace("<STIME>", round((microtime(true) - $t), 3), $out);
?>