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

		// インスタンス生成
		$searcher = new \tomk79\searchInDirectory\main(
			array(
				__DIR__.'/testdata/dir/',
			),
			array(
				'progress' => function( $_done, $_total ) use ( &$total, &$done ){
					$total = $_total;
					$done = $_done;
					// var_dump($_done.'/'.$_total);
				},
				'match' => function( $file, $result ) use ( &$matched ){
					// var_dump('Matched! '.$file);
					array_push($matched, $file);
				},
				'unmatch' => function( $file, $result ) use ( &$unmatched ){
					// var_dump('Unmatched! '.$file);
					array_push($unmatched, $file);
				},
				'error' => function( $file, $error ){
					// var_dump($file);
					// var_dump($error);
				},
			)
		);
		$this->assertTrue( is_object( $searcher ) );

		// 検索する
		$matched = array();
		$unmatched = array();
		$total = 0;
		$done = 0;
		$result = $searcher->start(
			'text',
			array(
				'filter' => array(
				) ,
				'ignore' => array(
				) ,
				'allowRegExp' => false,
				'ignoreCase' => false,
				'matchFileName' => false,
			)
		);
		// var_dump($result);
		// var_dump($matched);
		// var_dump($unmatched);



		$this->assertEquals( count($matched), 1 );
		$this->assertTrue( array_search('/a.html', $matched) !== false );
		$this->assertEquals( count($unmatched), 3 );
		$this->assertTrue( array_search('/b.html', $unmatched) !== false );
		$this->assertTrue( array_search('/test.html', $unmatched) !== false );
		$this->assertTrue( array_search('/c/d/e/f.html', $unmatched) !== false );
		$this->assertEquals( $total, 7 );
		$this->assertEquals( $done, 7 );


		// 検索する
		$matched = array();
		$unmatched = array();
		$total = 0;
		$done = 0;
		$result = $searcher->start(
			'Te.t',
			array(
				'filter' => array(
					'/./i',
				) ,
				'ignore' => array(
					'/c\/d/',
				) ,
				'allowRegExp' => true,
				'ignoreCase' => true,
				'matchFileName' => true,
			)
		);
		// var_dump($result);
		// var_dump($matched);
		// var_dump($unmatched);


		$this->assertEquals( count($matched), 3 );
		$this->assertTrue( array_search('/a.html', $matched) !== false );
		$this->assertTrue( array_search('/b.html', $matched) !== false );
		$this->assertTrue( array_search('/test.html', $matched) !== false );
		$this->assertEquals( count($unmatched), 1 );
		$this->assertTrue( array_search('/c/d/e/f.html', $unmatched) !== false );
		$this->assertEquals( $total, 7 );
		$this->assertEquals( $done, 7 );
	}

}
