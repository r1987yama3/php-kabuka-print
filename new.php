<?php
    /* ***************************************
     * 最新の株価と、その日にちを返すファイル。
     * 返されるデータの文字列は、
     * GETパラメータで、styleに対し、返される文字列の文章を
     * 設定する。
     * 設定方法は、どのように返して欲しいかの文字列をそのまま入力する。
     * また、日付を入れたい部分には、「%date%」と、株価を入れたい部分には「%kabuka%」
     * と入れることにより、値に置換される。
     * その後、URLエンコードされたURLでリクエストを送ればOK。
     * デフォルトでは、「最新の株価は%date%のデータの%kabuka%円です。」となっている。
     * *************************************** */

    require_once './update.php';

    if( isset( $_GET['style'] ) ) {
        $text = $_GET['style'];
    } else {
        $text = urlencode( "最新の株価は%date%のデータの%kabuka%円です。" );

    }

    // 株価データファイルを最新のものに更新し、
    // そのデータを$ddに格納する
    $dd = update();

    // 取得したデータの配列の長さを$iに格納する
    $i = count( $dd );

    // 最新のデータを出力する。
    $ret['date']    = $dd[$i-2][0];
    $ret['kabuka']  = $dd[$i-2][1];

    $text = urldecode( $text );
    $text = str_replace( "%date%", $ret['date'], $text );
    $text = str_replace( "%kabuka%", $ret['kabuka'], $text );

    echo $text;

?>
