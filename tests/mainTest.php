<?php
/**
 * test for tomk79/search-in-directory
 */
class mainTest extends PHPUnit\Framework\TestCase{
	private $fs;

	protected function setUp() : void{
		mb_internal_encoding('UTF-8');
		$this->fs = new tomk79\filesystem();
	}


	/**
	 * TEST
	 */
	public function test_main(){

		$this->assertTrue( is_dir(__DIR__.'/testdata') );

		$matched = array();

		// インスタンス生成
		$searcher = new \tomk79\searchInDirectory\main(
			__DIR__.'/testdata/dir/',
			array(
				'progress' => function( $done, $max ){},
				'match' => function( $file, $result ) use ( &$matched ){
					array_push($matched, $file);
				},
				'unmatch' => function( $file, $result ){},
				'error' => function( $file, $error ){},
			)
		);
		$this->assertTrue( is_object( $searcher ) );

		// 検索する
		$result = $searcher->start(
			'text',
			array()
		);
		// var_dump($result);


		$this->assertEquals( count($matched), 1 );
	}

}
