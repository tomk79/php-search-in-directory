<?php
namespace tomk79\searchInDirectory;

/**
 * Search In Directory: main class
 */
class main{

	/** 検索対象ディレクトリ */
	private $targets;

	/** オプション */
	private $options;

	/**
	 * constructor
	 * @param string $target 検索対象のディレクトリ
	 * @param array $options Options
	 */
	public function __construct( $targets, $options = array() ){
		// 検索対象ディレクトリ
		$this->targets = $targets;
		if( !is_array($this->targets) ){
			$this->targets = array( $this->targets );
		}
		foreach( $this->targets as $idx=>$path_dir ){
			if( !is_dir( $path_dir ) ){
				trigger_error( '$target['.$idx.'] is NOT a directory.' );
			}
			if( !is_readable( $path_dir ) ){
				trigger_error( '$target['.$idx.'] is NOT readable.' );
			}
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
		$search = new search($this, $this->options, $this->targets);
		$search->search($keyword, $cond);
		return true;
	}

}
