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

		if( is_file( __DIR__.'/testdata/_db.sqlite' ) ){
			$this->fs->rm( __DIR__.'/testdata/_db.sqlite' );
		}
		$this->assertTrue( is_dir(__DIR__.'/testdata') );
		$this->assertFalse( is_file(__DIR__.'/testdata/_db.sqlite') );

		// DB接続
		$pdo = new \PDO(
			'sqlite:'.__DIR__.'/testdata/_db.sqlite',
			null,
			null,
			array()
		);

		// インスタンス生成
		$searcher = new \tomk79\searchInDirectory\main(
			__DIR__.'/testdata/dir/',
			array(
				'pdo' => $pdo,
			)
		);
		$this->assertTrue( is_object( $searcher ) );
		$this->assertTrue( is_object( $searcher->pdo() ) );

		// マイグレーション
		$searcher->migrate();

		// インデックスを更新する
		$searcher->update_index();

		// 検索する
		$result = $searcher->search('text');
		// var_dump($result);
		$this->assertEquals( count($result), 1 );
	}

}
