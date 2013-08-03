<?php
class SparkTest extends PHPUnit_Framework_TestCase
{
   private $_spark;

   protected function setUp() {
      $this->_spark = new Spark\Core\Spark();
   }
   
   public function testLoadingTags() {
      $this->assertEquals(1, count($this->_spark->getTags()));

		$this->_spark->addTag("Test", function($html, $inner) { return "Test"; });

      $this->assertEquals(2, count($this->_spark->getTags()));

		$this->_spark->addTag("Test", function($html, $inner) { return "Test 2"; });

      $this->assertEquals(2, count($this->_spark->getTags()));
   }
   
   public function testSimpleRender() {
		$this->_spark->addTag("Test", function($html, $inner) { return "Example"; });

   	$html = '<html><head><title>Test</title></head><body><SparkTest></SparkTest></body></html>';
   	$expected_html = '<html><head><title>Test</title></head><body>Example</body></html>';
   	$result = $this->_spark->run($html);

      $this->assertEquals($expected_html, $result);
   }
   
   public function testNestedRender() {
		// This is a Boolean switch
		$this->_spark->addTag("Switch", function($html, $inner) {
		    if ($inner == "True") return "False";
		    if ($inner == "False") return "True";
		    return "Error";
		});

   	$html = '<html><head><title>Test</title></head><body><SparkSwitch><SparkSwitch><SparkSwitch>False</SparkSwitch></SparkSwitch></SparkSwitch></body></html>';
   	$expected_html = '<html><head><title>Test</title></head><body>True</body></html>';
   	$result = $this->_spark->run($html);

      $this->assertEquals($expected_html, $result);
   }
}