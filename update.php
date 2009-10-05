<?php
    /* ***************************************
     * 最新の日経平均株価の値を拾ってきて、
     * その値でデータファイルを更新する。
     * 更新前の最新のデータの日付と、更新
     * しようとしているデータの日付が同一
     * であれば、ファイルを更新しない。
     *
     * 日経平均株価のデータは、Yahooファイナンス
     * (http://stocks.finance.yahoo.co.jp/stocks/history/?code=998407.O)
     * を使用している。
     * *************************************** */

    // 日経平均株価の取得先URL
    define( "_URL", "http://stocks.finance.yahoo.co.jp/stocks/detail/?code=998407" );

    // 日経平均株価のデータを保存するファイルを指定
    define( "_DATA_FILE", "data.txt" );


    // HTMLパース用ライブラリの読み込み
    require_once "simple_html_dom.php";


    function update() {

       /* ***************************************
        * 株価データの取得
        * *************************************** */
        $dom = file_get_dom( _URL );
        foreach( $dom->find( 'span[class=yjFL]' ) as $node ) {
            $kabuka = $node->innertext;
            $kabuka = str_replace( ",", "", $kabuka );
            $kabuka = floatval( $kabuka );
            break;
        }
        foreach( $dom->find( 'td[class=yjSt]' ) as $node ) {
            $date = $node->innertext;
            $date = substr( $date, 20, 5 );
            break;
        }


       /* ***************************************
        * 株価データが終値であるかをチェック。
        * さらに終値であれば、データファイルの
        * 値と重複していないかをチェック。
        * もし重複していないデータであれば、そ
        * のデータをファイルに書き込み更新する
        * *************************************** */

        $d = file_get_contents( _DATA_FILE );
        $d = split( "\n", $d );
        for( $i=0; isset( $d[$i] ); $i++ ) {
            $dd[] = split( ", ", $d[$i] );
        }


        if( !ereg( "[0-9][0-9]/[0-9][0-9]", $date ) ) {
            // 何もしない
        } else {
            if( strcmp( $dd[$i-2][0], $date )==0 ) {
                // Do Nothing
            } else {
                $fp = fopen( _DATA_FILE, "a" );
                fwrite( $fp, "".$date.", ".$kabuka."\n" );
                fclose( $fp );

                $dd[$i-1][0] = $date;
                $dd[$i-1][1] = $kabuka;
                $i++;

            }
        }
        return $dd;
    }



?>
