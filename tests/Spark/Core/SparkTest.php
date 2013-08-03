<?php
class SparkTest extends PHPUnit_Framework_TestCase
{
   private $_spark;

   protected function setUp() {
      $this->_spark = new Spark\Core\Spark();
   }
   
   public function testLoadedTags() {
      $this->assertEquals(1, count($this->_spark->getTags()));
   }
}