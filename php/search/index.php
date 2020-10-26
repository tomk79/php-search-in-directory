<?php
namespace tomk79\searchInDirectory;

/**
 * Search In Directory: update_index class
 */
class search{

	/** Search In Directory Object */
	private $searcher;

	/** 検索対象ディレクトリ */
	private $path_dir;

	/** キーワード */
	private $keyword;

	/** 検索条件 */
	private $cond;

	/** オプション */
	private $options;

	/** filesystem */
	private $fs;

	/**
	 * constructor
	 * @param object $searcher Search In Directory Object
	 * @param string $path_dir 検索対象のディレクトリ
	 */
	public function __construct( $searcher, $options, $path_dir ){
		// Search In Directory Object
		$this->searcher = $searcher;

		// オプション
		$this->options = $options;

		// 検索対象ディレクトリ
		$this->path_dir = $path_dir;

		// fs
		$this->fs = new \tomk79\filesystem();
	}

	/**
	 * 検索を実行する
	 */
	public function search( $keyword, $cond = array() ){
		$this->keyword = $keyword;
		$this->cond = $cond;
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

		$exp = '/'.preg_quote($this->keyword, '/').'/';

		$result = array(
			'matched' => true,
			'type' => 'file',
			'count' => 0,
			'highlights' => array(),
		);
		if( preg_match($exp, $body) ){
			$result['matched'] = true;
			$this->options['match']( $realpath_file, $result );
		}else{
			$result['matched'] = false;
			$this->options['unmatch']( $realpath_file, $result );
		}

		return;
	}

}
