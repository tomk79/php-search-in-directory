# php-search-in-directory
指定ディレクトリ内のファイルを検索する。



## インストール手順 - Install

```
$ composer require tomk79/search-in-directory
```


## 使い方 - Usage

```php
$searcher = new \tomk79\searchInDirectory\main(
    array(
        // 検索対象とするディレクトリを列挙する
        '/path/to/target_dir/',
    ),
    array(
        'temporary_data_dir' => '/path/to/temporary_data_dir/', // 一時データ保存ディレクトリ(任意)
        'progress' => function( $_done, $_total ) use ( &$total, &$done ){
            // 進行状況を受けるコールバック関数
            var_dump($_done.'/'.$_total);
            $total = $_total;
            $done = $_done;
        },
        'match' => function( $file, $result ) use ( &$matched ){
            // 検索にマッチしたファイルの情報を受けるコールバック関数
            var_dump('Matched! '.$file);
            array_push($matched, $file);
        },
        'unmatch' => function( $file, $result ) use ( &$unmatched ){
            // 検索にマッチしなかったファイルの情報を受けるコールバック関数
            var_dump('Unmatched! '.$file);
            array_push($unmatched, $file);
        },
        'error' => function( $file, $error ){
            // 検索エラー情報を受けるコールバック関数
            var_dump($file);
            var_dump($error);
        },
    )
);

// 検索する
$matched = array();
$unmatched = array();
$total = 0;
$done = 0;
$result = $searcher->start(
    'text', // 検索キーワード
    array(
        'filter' => array(
            // ここに列挙するパターンにマッチしないパスは除外する
            '/./i',
        ) ,
        'ignore' => array(
            // ここに列挙するパターンにマッチするパスは除外する
            '/\.git/',
        ) ,
        'allowRegExp' => false, // true = 検索キーワード中に正規表現を使えるようにする
        'ignoreCase' => false, // true = 大文字・小文字を区別しない
        'matchFileName' => false, // true = ファイル名にもマッチさせる
    )
);
var_dump($matched);
var_dump($done.'/'.$total);
```



## 更新履歴 - Change log

### tomk79/search-in-directory v0.1.0 (リリース日未定)

- 初期化オプション `temporary_data_dir` を追加。
- 重複起動を防止するロック機能を追加。
- 検索のキャンセル機能を追加。

### tomk79/search-in-directory v0.0.1 (2020年10月29日)

- Initial Release.


## 開発者向け情報 - for Developer

### テスト - Test

```
$ cd {$documentRoot}
$ php vendor/phpunit/phpunit/phpunit
```


### ドキュメント出力 - phpDocumentor

```
$ composer run-script documentation
```


## ライセンス - License

Copyright (c)Tomoya Koyanagi<br />
MIT License https://opensource.org/licenses/mit-license.php


## 作者 - Author

- Tomoya Koyanagi <tomk79@gmail.com>
- website: <https://www.pxt.jp/>
- Twitter: @tomk79 <https://twitter.com/tomk79/>
