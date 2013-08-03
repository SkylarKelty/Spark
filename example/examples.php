<?php
$t = microtime(true);
require_once("src/loader.php");
?>

<!DOCTYPE html>
<html>
    <head>
        <SparkHeader>Examples | Spark</SparkHeader>
    </head>
    <body>
        <div class="container">
            <h1>Examples of custom tags</h1>
            <div class="tests">
                <p>Non-Nested boolean switch result: <SparkSwitch>False</SparkSwitch></p>
                <p>Nested boolean switch result:
                    <SparkSwitch>
                        <SparkSwitch>True</SparkSwitch>
                    </SparkSwitch>
                </p>
                <p>Multi-Nested boolean switch result:
                    <SparkSwitch>
                            <SparkSwitch>
                                <SparkSwitch>False</SparkSwitch>
                            </SparkSwitch>
                    </SparkSwitch>
                </p>
                <p>Nested boolean logic result:
                    <SparkLogic>
                            <SparkPass>
                                True
                            </SparkPass>
                            <SparkPass>
                                False
                            </SparkPass>
                    </SparkLogic>
                </p>
            </div>
        </div>
        <div class="version">
            <p>Rendered with <SparkVersion /></p>
        </div>
    </body>
</html>

<?php
require_once("src/run.php");
print "\n" . '<!-- Page loaded in ' . round((microtime(true) - $t), 3) . ' seconds -->';
?>