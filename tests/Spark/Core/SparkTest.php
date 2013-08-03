<?php
class SparkTest extends PHPUnit_Framework_TestCase
{
   private $_spark;

   protected function setUp() {
      $this->_spark = new Spark\Core\Spark();
   }
   
   public function testNamespace() {
      $this->_spark->setNamespace("Testing");

      $this->assertEquals("Testing", $this->_spark->getNamespace());

      $this->_spark->setNamespace("Spark");

      $this->assertEquals("Spark", $this->_spark->getNamespace());
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
   
   public function testEmptyTags() {
      $this->_spark->addTag("Test", function($html, $inner) { return "Example"; });

      $html = '<html><head><title>Test</title></head><body><SparkTest /></body></html>';
      $expected_html = '<html><head><title>Test</title></head><body>Example</body></html>';
      $result = $this->_spark->run($html);

      $this->assertEquals($expected_html, $result);

      $html = '<html><head><title>Test</title></head><body><SparkTest /><SparkTest /></body></html>';
      $expected_html = '<html><head><title>Test</title></head><body>ExampleExample</body></html>';
      $result = $this->_spark->run($html);

      $this->assertEquals($expected_html, $result);

      $html = '<html><head><title>Test</title></head><body><SparkTest /><SparkTest></SparkTest></body></html>';
      $expected_html = '<html><head><title>Test</title></head><body>ExampleExample</body></html>';
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
   
   public function testNamespaceCallback() {
      $spark = new Spark\Core\Spark("Spark", function($html, $inner) {
         return "Caught It!";
      });

      $this->assertEquals("Spark", $this->_spark->getNamespace());


      $html = '<html><head><title>Test</title></head><body><SparkTest>test</SparkTest><SparkTest2>test!</SparkTest2></body></html>';
      $expected_html = '<html><head><title>Test</title></head><body>Caught It!Caught It!</body></html>';
      $result = $spark->run($html);

      $this->assertEquals($expected_html, $result);
   }

   public function testTagName() {
      $this->assertEquals('test', $this->_spark->getTagName("<test>"));
      $this->assertEquals('test', $this->_spark->getTagName("<test a='l'>"));
      $this->assertEquals('test', $this->_spark->getTagName("</test>"));
   }
   
   public function testBadTags() {
      $html = '<html><head><title>Test</title></head><body><SparkVersion2></SparkVersion2></body></html>';
      $expected_html = '<html><head><title>Test</title></head><body></body></html>';
      $result = $this->_spark->run($html);

      $this->assertEquals($expected_html, $result);

      $this->assertEquals(array('SparkVersion2 is not a valid tag!'), $this->_spark->getErrors());
   }
   
   public function testBadHTML() {

      $this->_spark->addTag("Test", function($html, $inner) { return "Test"; });

      // Test bad namespace HTML
      $html = '<html><head><title>Test</title></head><body><SparkTest></SparkTest></SparkVersion></body></html>';
      $expected_html = '<html><head><title>Test</title></head><body>Test</SparkVersion></body></html>';
      $result = $this->_spark->run($html);
      $this->assertEquals($expected_html, $result);
      $this->assertEquals(array('Bad markup: Extra or misplaced closing tag found for element: SparkVersion'), $this->_spark->getErrors());

      // Test bad namespace HTML
      $html = '<html><head><title>Test</title></head><body><SparkTest></SparkTest><SparkTest></body></html>';
      $expected_html = '<html><head><title>Test</title></head><body>Test<SparkTest></body></html>';
      $result = $this->_spark->run($html);
      $this->assertEquals($expected_html, $result);
      $this->assertEquals(array('Bad markup: No closing tag found for element: SparkTest'), $this->_spark->getErrors());

      // Test bad namespace HTML
      $html = '<html><head><title>Test</title></head><body><SparkTest></SparkTest><SparkTest><SparkTest></SparkTest></body></html>';
      $expected_html = '<html><head><title>Test</title></head><body>Test<SparkTest></body></html>';
      $result = $this->_spark->run($html);
      $this->assertEquals($expected_html, $result);
      $this->assertEquals(array('Bad markup: No closing tag found for element: SparkTest'), $this->_spark->getErrors());

      // Test bad normal HTML
      $html = '<html><head><title>Test</title></head><body><SparkTest></SparkTest></p></body></html>';
      $expected_html = '<html><head><title>Test</title></head><body>Test</p></body></html>';
      $result = $this->_spark->run($html);
      $this->assertEquals($expected_html, $result);
      $this->assertEquals(array(), $this->_spark->getErrors());
   }
}