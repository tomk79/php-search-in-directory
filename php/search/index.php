<?php
namespace tomk79\searchInDirectory;

/**
 * Search In Directory: update_index class
 */
class search{

	/** filesystem */
	private $fs;

	/** Search In Directory Object */
	private $searcher;

	/** 検索対象ディレクトリ */
	private $targets;

	/** 検索対象ディレクトリ */
	private $realpath_current_target;

	/** キーワード */
	private $keyword;

	/** 検索条件 */
	private $cond;

	/** オプション */
	private $options;

	/** 検索対象の全件数 */
	private $total = 0;

	/** 検索済みの件数 */
	private $done = 0;

	/** ヒットしたファイル数 */
	private $hit = 0;

	/**
	 * constructor
	 * @param object $searcher Search In Directory Object
	 * @param string $target 検索対象のディレクトリ
	 */
	public function __construct( $searcher, $options, $targets ){
		// Search In Directory Object
		$this->searcher = $searcher;

		// オプション
		$this->options = $options;

		// 検索対象ディレクトリ
		$this->targets = $targets;

		// fs
		$this->fs = new \tomk79\filesystem();
	}

	/**
	 * 検索を実行する
	 */
	public function search( $keyword, $cond = array() ){
		$this->keyword = $keyword;
		$this->cond = $cond;
		foreach( $this->targets as $target ){
			$this->realpath_current_target = $target;
			$this->scan_dir_r();
		}
	}

	/**
	 * ディレクトリを再帰的にスキャンする
	 */
	private function scan_dir_r( $path = null ){
		$ls = $this->fs->ls( $this->realpath_current_target.'/'.$path );
		$this->total = $this->total + count($ls);

		foreach( $ls as $basename ){
			if( is_dir( $this->realpath_current_target.'/'.$path.'/'.$basename ) ){
				$this->scan_dir_r( $path.'/'.$basename );
				$this->done ++;
			}elseif( is_file( $this->realpath_current_target.'/'.$path.'/'.$basename ) ){
				$this->scan_file( $path.'/'.$basename );
				$this->done ++;
			}

			$this->options['progress']($this->done, $this->total);
		}
	}

	/**
	 * ファイルをスキャンする
	 */
	private function scan_file( $realpath_file ){
		// var_dump($realpath_file);
		$body = $this->fs->read_file( $this->realpath_current_target.'/'.$realpath_file );

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
			$this->hit ++;
		}else{
			$result['matched'] = false;
			$this->options['unmatch']( $realpath_file, $result );
		}

		return;
	}

}
