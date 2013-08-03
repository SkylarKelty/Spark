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
}