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

	/** $fs */
	private $fs;

	/**
	 * constructor
	 * @param string $target 検索対象のディレクトリ
	 * @param array $options 初期化オプション
	 *
	 * - `temporary_data_dir` => String : ディレクトリのパス
	 * - `progress` => function( $done, $total ) : 進行状況を受けるコールバック関数
	 * - `match` => function( $file, $result ) : 検索にマッチしたファイルの情報を受けるコールバック関数
	 * - `unmatch` => function( $file, $result ) : 検索にマッチしなかったファイルの情報を受けるコールバック関数
	 * - `error` => function( $file, $error ) : 検索エラー情報を受けるコールバック関数
	 */
	public function __construct( $targets, $options = array() ){
		$this->fs = new \tomk79\filesystem();

		// --------------------------------------
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

		// --------------------------------------
		// 初期化オプション
		$options = (array) $options;
		if( array_key_exists('temporary_data_dir', $options) && is_string($options['temporary_data_dir']) && strlen($options['temporary_data_dir']) && is_dir($options['temporary_data_dir']) && is_writable($options['temporary_data_dir']) ){
			$options['temporary_data_dir'] = $this->fs->get_realpath( $options['temporary_data_dir'].'/' );
		}else{
			$options['temporary_data_dir'] = false;
		}
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
