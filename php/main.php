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
		$this->options = $options;
	}

	/**
	 * 検索を実行する
	 * @param string $keyword キーワード
	 */
	public function start( $keyword, $cond = array() ){
		$search = new search($this, $this->options, $this->path_dir);
		$result = $search->search($keyword, $cond);
		return $result;
	}

}
