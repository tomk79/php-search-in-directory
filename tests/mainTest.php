<?php
/**
 * test for tomk79/search-in-directory
 */
class mainTest extends PHPUnit_Framework_TestCase{
	private $fs;

	public function setup(){
		mb_internal_encoding('UTF-8');
		$this->fs = new tomk79\filesystem();
	}


	/**
	 * TEST
	 */
	public function testMain(){
		$this->assertEquals( 1, 1 );
		$this->assertTrue( is_dir(__DIR__.'/testdata') );
	}

}
