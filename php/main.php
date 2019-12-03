<?php
namespace tomk79\searchInDirectory;

/**
 * Search In Directory: main class
 */
class main{

	/** 検索対象ディレクトリ */
	private $path_dir;

	/** オプション */
	private $options;

	/** PDO */
	private $pdo;

	/**
	 * constructor
	 * @param string $path_dir 検索対象のディレクトリ
	 * @param array $options Options
	 */
	public function __construct( $path_dir, $options = array() ){
		// 検索対象ディレクトリ
		$this->path_dir = $path_dir;
		if( !is_dir( $this->path_dir ) ){
			trigger_error( '$path_dir is NOT a directory.' );
		}
		if( !is_readable( $this->path_dir ) ){
			trigger_error( '$path_dir is NOT readable.' );
		}

		// オプション
		$options = (array) $options;
		if( array_key_exists('pdo', $options) ){
			$this->pdo = $options['pdo'];
		}
		$this->options = $options;
	}

	/**
	 * PDOを取得
	 */
	public function pdo(){
		return $this->pdo;
	}

	/**
	 * 初期化する
	 */
	public function migrate(){
		$result = $this->pdo()->query('CREATE TABLE search_in_directory (
			filename TEXT,
			title TEXT,
			body TEXT,
			insert_date DATETIME,
			last_update_date DATETIME
		);');
		return true;
	}

	/**
	 * インデックスを更新する
	 */
	public function update_index(){
		$updateIndex = new update_index( $this, $this->path_dir );
		$updateIndex->update();
	}

	/**
	 * 検索する
	 * @param string $keyword キーワード
	 */
	public function search( $keyword ){
		$data = array(
			'keyword' => "%".$keyword."%",
		);

		$stmt = $this->pdo()->prepare('SELECT * FROM search_in_directory WHERE body LIKE :keyword ');
		$stmt->bindParam( ':keyword', $data['keyword'], \PDO::PARAM_STR );
		$stmt->execute();
		$result = $stmt->fetchAll();
		return $result;
	}

}
