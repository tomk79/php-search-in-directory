<?php
namespace tomk79\searchInDirectory;

/**
 * Status management class
 */
class statusMgr{

	/** filesystem */
	private $fs;

	/** Search In Directory Object */
	private $main;

	/** オプション */
	private $options;

	/** 一時データ保存ディレクトリのパス */
	private $realpath_temporary_data_dir;

	/** 実行中ロックファイルのパス */
	private $realpath_lockfile;

	/** キャンセル要求ファイルのパス */
	private $realpath_cancel_request;

	/**
	 * constructor
	 * @param object $main Search In Directory Object
	 * @param string $target 検索対象のディレクトリ
	 */
	public function __construct( $main, $options, $fs ){
		$this->main = $main;
		$this->options = $options;
		$this->fs = $fs;


		$this->realpath_temporary_data_dir = $this->fs->get_realpath($this->options['temporary_data_dir'].'/');
		$this->realpath_lockfile = $this->realpath_temporary_data_dir.'lock.txt';
		$this->realpath_cancel_request = $this->realpath_temporary_data_dir.'cancel_request.txt';
	}

	/**
	 * アプリケーションロックする。
	 *
	 * @return bool ロック成功時に `true`、失敗時に `false` を返します。
	 */
	public function lock(){
		if( !$this->is_temporary_data_dir_available() ){
			return true;
		}
		$lockfilepath = $this->realpath_lockfile;
		$timeout_limit = 5;

		if( !is_dir( dirname( $lockfilepath ) ) ){
			$this->fs->mkdir_r( dirname( $lockfilepath ) );
		}

		// PHPのFileStatusCacheをクリア
		clearstatcache();

		$i = 0;
		while( $this->is_locked() ){
			$i ++;
			if( $i >= $timeout_limit ){
				return false;
				break;
			}
			sleep(1);

			// PHPのFileStatusCacheをクリア
			clearstatcache();
		}
		$src = '';
		$src .= 'ProcessID='.getmypid()."\r\n";
		$src .= date( 'Y-m-d H:i:s' , time() )."\r\n";
		$RTN = $this->fs->save_file( $lockfilepath , $src );
		return	$RTN;
	} // lock()

	/**
	 * アプリケーションロックされているか確認する。
	 *
	 * @return bool ロック中の場合に `true`、それ以外の場合に `false` を返します。
	 */
	public function is_locked(){
		if( !$this->is_temporary_data_dir_available() ){
			return false;
		}
		$expire = 60;
		$lockfilepath = $this->realpath_lockfile;
		$lockfile_expire = $expire;

		// PHPのFileStatusCacheをクリア
		clearstatcache();

		if( $this->fs->is_file($lockfilepath) ){
			if( ( time() - filemtime($lockfilepath) ) > $lockfile_expire ){
				// 有効期限を過ぎていたら、ロックは成立する。
				return false;
			}
			return true;
		}
		return false;
	} // is_locked()

	/**
	 * アプリケーションロックを解除する。
	 *
	 * @return bool ロック解除成功時に `true`、失敗時に `false` を返します。
	 */
	public function unlock(){
		if( !$this->is_temporary_data_dir_available() ){
			return true;
		}
		$lockfilepath = $this->realpath_lockfile;
		$cancel_request_filepath = $this->realpath_cancel_request;

		// PHPのFileStatusCacheをクリア
		clearstatcache();

		$rtn = true;
		if( !unlink( $lockfilepath ) ){
			$rtn = false;
		}

		// 中断要求がある場合はこれも削除
		if( is_file($cancel_request_filepath) ){
			if( !unlink( $cancel_request_filepath ) ){
				$rtn = false;
			}
		}
		return $rtn;
	} // unlock()

	/**
	 * アプリケーションロックファイルの更新日を更新する。
	 *
	 * @return bool 成功時に `true`、失敗時に `false` を返します。
	 */
	public function touch_lockfile(){
		if( !$this->is_temporary_data_dir_available() ){
			return true;
		}
		$lockfilepath = $this->realpath_lockfile;

		// PHPのFileStatusCacheをクリア
		clearstatcache();
		if( !is_file( $lockfilepath ) ){
			return false;
		}

		return touch( $lockfilepath );
	} // touch_lockfile()


	/**
	 * 検索処理の中断要求を発行する
	 *
	 * @return bool 成功時に `true`、失敗時に `false` を返します。
	 */
	public function cancel_request(){
		if( !$this->is_temporary_data_dir_available() ){
			return false;
		}
		if( !$this->is_locked() ){
			// 実行中ではないためキャンセルできません。
			return false;
		}

		$cancel_request_filepath = $this->realpath_cancel_request;
		$src = '';
		$src .= 'Cancel Request'."\r\n";
		$src .= date( 'Y-m-d H:i:s' , time() )."\r\n";
		$RTN = $this->fs->save_file( $cancel_request_filepath , $src );
		return $RTN;
	}

	/**
	 * 検索処理の中断要求が発行されているか確認する
	 *
	 * @return bool 中断要求がある時に `true`、ない時に `false` を返します。
	 */
	public function is_cancel_request(){
		if( !$this->is_temporary_data_dir_available() ){
			return false;
		}
		$cancel_request_filepath = $this->realpath_cancel_request;

		clearstatcache();

		if( $this->fs->is_file($cancel_request_filepath) ){
			return true;
		}
		return false;
	}


	/**
	 * 一時データディレクトリが利用可能か確認する
	 * @return bool 利用可能な場合に `true`、利用できない場合に `false` を返します。
	 */
	private function is_temporary_data_dir_available(){
		if( !strlen($this->realpath_temporary_data_dir) ){
			return false;
		}
		if( !is_dir($this->realpath_temporary_data_dir) || !is_writable($this->realpath_temporary_data_dir) ){
			return false;
		}
		return true;
	}
}
