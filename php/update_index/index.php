<?php
namespace tomk79\searchInDirectory;

/**
 * Search In Directory: update_index class
 */
class update_index{

	/** Search In Directory Object */
	private $searcher;

	/** 検索対象ディレクトリ */
	private $path_dir;

	/** filesystem */
	private $fs;

	/**
	 * constructor
	 * @param object $searcher Search In Directory Object
	 * @param string $path_dir 検索対象のディレクトリ
	 */
	public function __construct( $searcher, $path_dir ){
		// Search In Directory Object
		$this->searcher = $searcher;

		// 検索対象ディレクトリ
		$this->path_dir = $path_dir;

		// fs
		$this->fs = new \tomk79\filesystem();
	}

	/**
	 * インデックスを更新する
	 */
	public function update(){
		$this->scan_dir_r();
	}

	/**
	 * ディレクトリを再帰的にスキャンする
	 */
	private function scan_dir_r( $path = null ){
		$ls = $this->fs->ls( $this->path_dir.'/'.$path );
		foreach( $ls as $basename ){
			if( is_dir( $this->path_dir.'/'.$path.'/'.$basename ) ){
				$this->scan_dir_r( $path.'/'.$basename );
			}elseif( is_file( $this->path_dir.'/'.$path.'/'.$basename ) ){
				$this->scan_file( $path.'/'.$basename );
			}
		}
	}

	/**
	 * ファイルをスキャンする
	 */
	private function scan_file( $realpath_file ){
		// var_dump($realpath_file);
		$body = $this->fs->read_file( $this->path_dir.'/'.$realpath_file );

		$stmt = $this->searcher->pdo()->prepare('INSERT INTO search_in_directory (filename, title, body, insert_date, last_update_date) VALUES (:filename, :title, :body, :insert_date, :last_update_date);');
		// var_dump($stmt);
		$data = array(
			'filename' => $realpath_file,
			'title' => '',
			'body' => $body,
			'insert_date' => '',
			'last_update_date' => '',
		);
		$stmt->bindParam( ':filename', $data['filename'], \PDO::PARAM_STR );
		$stmt->bindParam( ':title', $data['title'], \PDO::PARAM_STR );
		$stmt->bindParam( ':body', $data['body'], \PDO::PARAM_STR );
		$stmt->bindParam( ':insert_date', $data['insert_date'], \PDO::PARAM_STR );
		$stmt->bindParam( ':last_update_date', $data['last_update_date'], \PDO::PARAM_STR );
		$stmt->execute();
	}

}
