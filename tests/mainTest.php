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
		$unmatched = array();
		$total = 0;
		$done = 0;

		// インスタンス生成
		$searcher = new \tomk79\searchInDirectory\main(
			array(
				__DIR__.'/testdata/dir/',
			),
			array(
				'progress' => function( $_done, $_max ) use ( &$total, &$done ){
					$total = $_max;
					$done = $_done;
					var_dump($_done.'/'.$_max);
				},
				'match' => function( $file, $result ) use ( &$matched ){
					var_dump('Matched!', $file);
					array_push($matched, $file);
				},
				'unmatch' => function( $file, $result ) use ( &$unmatched ){
					array_push($unmatched, $file);
				},
				'error' => function( $file, $error ){
					var_dump($file, $error);
				},
			)
		);
		$this->assertTrue( is_object( $searcher ) );

		// 検索する
		$result = $searcher->start(
			'text',
			array(
				'filter' => array(
					'/./i',
				) ,
				'ignore' => array(
					'/\.git/',
				) ,
				'allowRegExp' => false,
				'ignoreCase' => false,
				'matchFileName' => false,
			)
		);
		// var_dump($result);


		$this->assertEquals( count($matched), 1 );
		$this->assertEquals( $total, 6 );
		$this->assertEquals( $done, 6 );
	}

}
