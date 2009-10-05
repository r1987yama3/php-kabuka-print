<?php
    /* ***************************************
     * 最近10日間の日経平均株価のデータを
     * 折れ線グラフで出力するグラフパーツ
     * 
     * 利用するには、当ファイルをPHPなどから
     * includeするだけでOK。
     * インクルード時にGETでwidth, heightを指定
     * することにより、画像の横幅、縦幅を指定する
     * ことが可能。なお、これらの初期値は、
     * 横幅250px、縦幅150pxを使っている。
     * 
     * グラフの描画には、Google Chart APIを利用
     * *************************************** */

    // 出力するグラフの横幅を設定
    if( isset( $_GET['width'] ) ) {
        $GraghWidth = $_GET['width'];
    } else {
        $GraghWidth = 250;
    }

    // 出力するグラフの縦幅を設定
    if( isset( $_GET['height'] ) ) {
        $GraghHeight = $_GET['height'];
    } else {
        $GraghHeight = 150;
    }

    // グラフにするデータの日数を指定する
    if( isset( $_GET['count'] ) ) {
        $count = $_GET['count'];
    } else {
        $count = 10;
    }

    // グラフのタイトルを設定する
    $title = "%start% - %end%の日経平均株価の推移";


    // 株価データファイルのアップロード関数を読み込む
    require_once './update.php';

    // 株価データファイルを最新のものに更新し、
    // そのデータを$ddに格納する
    $dd = update();

    // 取得したデータの個数を$iに格納する
    $i = count( $dd );


    // 直近10日間の最大値・最小値を探す
    // minに最小値、maxに最大値が格納
    $min = 99999;
    $max = 0;
    for( $k=0; $k<$count; $k++ ) {
        // 最小値を探す
        if( $min > $dd[$i-2-$k][1] ) {
            $min = $dd[$i-2-$k][1];
        }

        // 最大値を探す
        if( $max < $dd[$i-2-$k][1] ) {
            $max = $dd[$i-2-$k][1];
        }
    }

    // データを20〜80に整えて、$chdに格納する
    // y=ax+bを用いて整形する。
    // xは実際の株価、yは整形後の値
    $a = 60 / ( $max - $min );
    $b = 20 - $min * $a;

    // グラフにするときのデータを計算。y=ax+bを利用
    // 同時に、そのデータをURLに付加する変数$chdに書き込む
    $chd = "";
    for( $k=0; $k<$count; $k++ ) {
        $chd .= $a * $dd[$i-2-$k][1] + $b;
        if( $k != $count-1 ) {
            $chd .= ",";
        }
    }

    // グラフの縦軸の値を設定
    // y=ax+bから、y=0,100のときのxの値をそれぞれ計算
    $gmin = ( -1 * $b ) / $a;
    $gmax = ( 100 - $b ) / $a;

    // グラフの横軸の日付を設定する。
    // 日付は、開始日、中間日、最終日を表示
    $start = $dd[$i-2-$k+1][0];
    $middle = $dd[$i-(3+$k)/2][0];
    $end = $dd[$i-2][0];



    // グラフのタイトルを設定（URLエンコードを実行）
    $title = str_replace( "%start%", $start, $title );
    $title = str_replace( "%end%", $end, $title );
    $title = mb_convert_encoding( $title, "utf-8", "auto" );
    $title = urlencode( $title );

    /* ***************************************
     * グラフを出力するためのAPIのURL
     * Google Chart APIの
     * http://code.google.com/intl/ja/apis/chart/ を利用
     *
     * 設定内容：
     *      グラフのサイズ
     *      グラフの種類
     *      グラフのデータ
     *      グラフの横軸、縦軸のラベル
     *      各データのマーカーを設定
     *      グラフのタイトル
     *      
     * *************************************** */
    $graph = 'http://chart.apis.google.com/chart?chs='.$GraghWidth.'x'.$GraghHeight.'&cht=lc&chd=t:'.$chd.'&chxt=x,y&chxl=0:|'.$start.'|'.$middle.'|'.$end.'&chxr=1,'.$gmin.','.$gmax.'&chm=o,ff9900,0,-1,10.0&chtt='.$title;


    // 出力
    $ie = imagecreatefrompng( $graph );
    header( 'Content-Type: image/jpeg' );
    imagepng($ie, NULL);

?>
