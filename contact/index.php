<?php
// header("Content-Security-Policy: script-src 'self' trickdart.tokyo;");
header('X-Frame-Options: SAMEORIGIN');
ini_set('display_errors', "Off");

session_start();
$success_message = "";
$error_message = [];
$max_count = 100;
// トークンの生成
if(empty($_SESSION['csrf_token'])){
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //トークンが送信されているかの確認
    if (!isset($_POST['csrf_token'])) {
        $error_message[] = '不正なリクエストです。';
    //トークンが合っているかの検証
    }else if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error_message[] = '不正なリクエストです';
    }

    // フォームからのデータを取得
    $data['name'] = htmlspecialchars($_POST["name"]);
    $data['kana'] = htmlspecialchars($_POST["kana"]);
    $data['mail'] = htmlspecialchars($_POST["mail"]);
    $data['year'] = htmlspecialchars($_POST["year"]);
    $data['month'] = htmlspecialchars($_POST["month"]);
    $data['day'] = htmlspecialchars($_POST["day"]);
    $data['tel'] = htmlspecialchars($_POST["tel"]);
    $data['message'] = htmlspecialchars($_POST["message"]);
    $data['confirmation'] = htmlspecialchars($_POST["confirmation"]);
  
    // 入力値のバリデーション
    if (empty($data['name'])) {
        $error_message[] = "お名前 を入力してください。";
    }else if(isset($data['name']) && mb_strlen($data['name']) > $max_count){
        $error_message[] = "お名前 は ".$max_count."文字以内で入力してください。";
    }else if (preg_match('/[&"\'<>]/', $data['name'])) {
        die('お名前 に使用できない文字が含まれています。');
    }

    $data['kana'] = mb_convert_kana($data['kana'],'Hc','utf-8');

    if (empty($data['kana'])) {
        $error_message[] = "ふりがな を入力してください。";
    }else if(isset($data['kana']) && mb_strlen($data['kana']) > $max_count){
        $error_message[] = "ふりがな は ".$max_count."文字以内で入力してください。";
    }else if(!preg_match("/^[ぁ-んー]+$/u",$data['kana'])){
        $error_message[] = "ふりがな は ひらがなで入力してください。";
    }else if (preg_match('/[&"\'<>]/', $data['kana'])) {
        die('ふりがな に使用できない文字が含まれています。');
    }
    if (empty($data['mail'])) {
        $error_message[] = "メールアドレス を入力してください。";
    }else if(isset($data['mail']) && mb_strlen($data['mail']) > $max_count){
        $error_message[] = "メールアドレス は 200文字以内で入力してください。";
    }else if (preg_match('/[&"\'<>]/', $data['mail'])) {
        die('メールアドレス に使用できない文字が含まれています。');
    }
    if (empty($data['tel'])) {
        $error_message[] = "電話番号 を入力してください。";
    }else if( isset($data['tel']) && !preg_match('/^(0{1}\d{1,4}-{0,1}\d{1,4}-{0,1}\d{4})$/', $data['tel'] ) ) {
        $error_message[] = "電話番号 を正しく入力してください。";
    }else if (preg_match('/[&"\'<>]/', $data['tel'])) {
        die('電話番号 に使用できない文字が含まれています。');
    }

    if (empty($data['year'])) {
        $error_message[] = "生年月日(年)  を入力してください。";
    }else if(isset($data['month']) && !is_numeric($data['year'])){
        $error_message[] = "生年月日(年) は 数値で入力してください。";
    }else if(isset($data['year']) && mb_strlen($data['year']) > 4){
        $error_message[] = "生年月日(年)  は 4文字以内で入力してください。";
    }else if(isset($data['year']) && (intval($data['year']) > date('Y') -1 || intval($data['year']) < 1900)){
        $error_message[] = "生年月日(年)  を正しく入力してください。";
    }else if (preg_match('/[&"\'<>]/', $data['year'])) {
        die('生年月日(年) に使用できない文字が含まれています。');
    }
    if (empty($data['month'])) {
        $error_message[] = "生年月日(月) を入力してください。";
    }else if(isset($data['month']) && !is_numeric($data['month'])){
        $error_message[] = "生年月日(月) は 数値で入力してください。";
    }else if(isset($data['month']) && mb_strlen($data['month']) > 2){
        $error_message[] = "生年月日(月) は 2文字以内で入力してください。";
    }else if(isset($data['month']) && (intval($data['month']) < 0 || intval($data['month']) > 12)){
        $error_message[] = "生年月日(月)  を正しく入力してください。";
    }else if (preg_match('/[&"\'<>]/', $data['month'])) {
        die('生年月日(月)  に使用できない文字が含まれています。');
    }
    if (empty($data['day'])) {
        $error_message[] = "生年月日(日)  を入力してください。";
    }else if(isset($data['day']) && !is_numeric($data['day'])){
        $error_message[] = "生年月日(日) は 数値で入力してください。";
    }else if(isset($data['day']) && mb_strlen($data['day']) > 2){
        $error_message[] = "生年月日(日)  は 2文字以内で入力してください。";
    }else if(isset($data['day']) && (intval($data['day']) < 0 || intval($data['day']) > 31)){
        $error_message[] = "生年月日(日)  を正しく入力してください。";
    }else if (preg_match('/[&"\'<>]/', $data['day'])) {
        die('生年月日(日) に使用できない文字が含まれています。');
    }


    if (empty($data['message'])) {
        $error_message[] = "備考欄 を入力してください。";
    }else if(isset($data['message']) && mb_strlen($data['message']) > 500){
        $error_message[] = "備考欄 は 500文字以内で入力してください。";
    }else if (preg_match('/[&"\'<>]/', $data['message'])) {
        die('備考欄 に使用できない文字が含まれています。');
    }

    // // 同意
    if(empty($data['confirmation'])){
        $error_message[] = "入力内容の確認がチェックされてません。";
    }else if(isset($data['confirmation']) && $data['confirmation'] != "1"){
        $error_message[] = "入力内容の確認がチェックされてません。";
    }else if (preg_match('/[&"\'<>]/', $data['confirmation'])) {
        die('入力内容の確認 に使用できない文字が含まれています。');
    }

    if (isset($data['name']) && isset($data['kana']) && isset($data['mail']) && isset($data['tel']) &&
    isset($data['message']) && isset($data['confirmation']) && count($error_message) == 0) {

        // メールの送信 
        $to = "yasunori_tanouchi@bloomf.jp";
        // 送信先のメールアドレスを指定 
        $subject = "ホームページよりお問い合わせがありました。";
        $message_body = "お名前: " . $data['name'] . "\n"; 
        $message_body .= "ふりがな: " . $data['kana'] . "\n"; 
        $message_body .= "メールアドレス: " . $data['mail'] . "\n";
        $message_body .= "電話番号: " . $data['tel'] . "\n";
        $message_body .= "生年月日: " . $data['year']."年".$data['month']."月".$data['day'] ."日". "\n";
        $message_body .= "備考欄:\n" . $data['message']; 
        $headers = "From: " . $data['mail'];

        $success_message = "お問い合わせを受け付けました。ありがとうございます！"; 
        if (mb_send_mail($to, $subject, $message_body, $headers)) { 
            $success_message = "お問い合わせを受け付けました。ありがとうございます！"; 
        } else { 
            $error_message[] = "メールの送信に失敗しました。後でもう一度お試しください。"; 
        }
         
        // 入力データ削除
        $data['name'] = null;
        $data['kana'] = null;
        $data['mail'] = null;
        $data['tel'] = null;
        $data['year'] = null;
        $data['month'] = null;
        $data['day'] = null;
        $data['message'] = null;
        $data['confirmation'] = null;
   }else {
        $error_message[] = "メールの送信に失敗しました。後でもう一度お試しください。"; 
   }
}
?>
<!DOCTYPE html>
<!--[if lt IE 9 ]><html class="no-js oldie" lang="en"> <![endif]-->
<!--[if IE 9 ]><html class="no-js oldie ie9" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html class="no-js" lang="ja">
<!--<![endif]-->

<head>

    <!--- basic page needs
    ================================================== -->
    <meta charset="utf-8">
    <title>お問い合わせ | エスリール株式会社</title>
    <meta name="description" content="エスリール株式会社">
    <meta name="keywords" content="お片付け,家財処分,引っ越し,買取等">
    <meta name="author" content="">
    <meta charset="utf-8" />

    <!-- mobile specific metas
    ================================================== -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSS
    ================================================== -->
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/vendor.css">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/contact.css">

    <!-- script
    ================================================== -->
    <script src="../js/modernizr.js"></script>
    <script src="../js/pace.min.js"></script>

    <!-- favicons
    ================================================== -->
    <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">
    <link rel="icon" href="../favicon.ico" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&display=swap" rel="stylesheet">

</head>

<body id="top">

    <!-- header
    ================================================== -->
    <header class="s-header">

        <div class="header-logo">
            <h1><a class="site-logo" href="/"><img src="../images/logo.png" alt="エスリール s-reel"></a></h1>
        </div>


        <div class="header-navi-wrap pc-only">
            <ul class="header-nav-wrap__list">
                <li class="arrow"><a href="/" title="top">TOP</a></li>
                <li class="arrow"><a href="/company/" title="company">会社概要</a></li>
                <li class="contact-btn-li">
                    <div class="contact-btn">
                        <a class="smoothscroll" href="#top" class="btn btn--orange btn--radius">お問い合わせ</a>
                    </div>
                </li>
                <li class="contact-tel">
                    <!-- <div class="contact-tel"> -->
                        <div class="tel-icon">
                            <img src="../images/tel-icon.png" alt="電話番号">
                        </div>
                        <div class="tel-number-area">
                            <a href="tel:0120-888-134" class="tel-number">0120-888-134</a>
                            <p>受付時間：年中無休(24時間対応)</p>
                        </div>
                    <!-- </div> -->
                </li>
            </ul>
        </div>

        <nav class="header-nav sp-only">

            <a href="#0" class="header-nav__close" title="close"><span>Close</span></a>

            <div class="header-nav__content">

                <ul class="header-nav__list">
                    <li class="arrow"><a href="/" title="top">TOP</a></li>
                    <li class="arrow"><a href="/company/" title="company">会社概要</a></li>
                    <li class="arrow"><a class="smoothscroll" href="#top" title="contact">お問い合わせ</a></li>
                </ul>
            </div>

            <!-- <div class="contact-tel">
                <div class="tel-icon">
                    <img src="images/tel-icon.png" alt="電話番号">
                </div>
                <div class="tel-number-area">
                    <a href="tel:0120-888-134" class="tel-number">0120-888-134</a>
                    <p>受付時間：年中無休(24時間対応)</p>
                </div>
            </div> -->
            
        </nav> <!-- end header-nav -->

        <!-- SP メニュー　-->
        <a class="header-menu-toggle sp-only opaque" href="#0">
            <span class="header-menu-icon"></span>
        </a>

    </header> <!-- end s-header -->


    <!-- home
    ================================================== -->
    <section id='top' class="top-area">
        <img src="../images/company/company-view.png" alt="お問い合わせ">
        <div class="message-title">
            <h2>CONTACT</h2>
            <h1>お問い合わせ</h1>
        <div>
    </section>


    <!-- バナー + 電話番号
    ================================================== -->
    <section id='top-contact' class="s-top-contact">

        <div class="intro-wrap">
                
            <div class="col-full top-banner">
                <div class="top-contact-item">
                    <div class="banner">
                        <img src="../images/banner01.png" alt="キャンペーン バナー1">
                    </div>
                </div>
            </div>

            <div class="col-full top-tel">
                <div class="top-contact-item" data-aos="fade-up">
                    <div class="text">お電話でもお気軽にお問い合わせください!!</div>
                    <div class="tel">0120-888-134</div>
                    <div class="time">受付時間：年中無休（２４時間対応）</div>
                </div>
            </div>
        </div> 

    </section> <!-- end s-message -->


    <!-- FORM 
    ================================================== -->
    <section id='form' class="s-form">

        <div class="intro-wrap">
                
            <div class="row section-header has-bottom-sep light-sep">
                <div class="col-full">
                    <h3 class="sub-title">FORM</h3>
                    <h2 class="display-2 display-2--light">フォームからお問い合わせ</h2>
                </div>
            </div> <!-- end section-header -->

        </div> <!-- end intro-wrap -->

        <div class="row form-content">
            <div class="col-full">

                <!-- エラーメッセージ -->
                <?php if (isset($error_message)): ?> 
                    <div class="message_area">
                    <?php foreach($error_message as $_message): ?> 
                        <p class="error"><?php echo $_message; ?></p> 
                    <?php endforeach; ?> 
                    </div>
                <?php endif; ?> 
                <!-- 完了メッセージ -->
                <?php if (isset($success_message)): ?> 
                    <div class="message_area">
                        <p class="success"><?php echo $success_message; ?></p> 
                    </div>
                <?php endif; ?>

                <form class="contact-form" method="POST" name="form1" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                    <div class="row form-list block-1-2 block-tab-full">
                        <div class="form-item">
                            <div>
                                <div class="label">お名前<span>*</span></div>
                                <div class="input">
                                    <input type="text" name="name" value="<?php echo $data['name']; ?>" maxlength="100">
                                </div>
                            </div>
                            <div>
                                <div class="label">ふりがな<span>*</span></div>
                                <div class="input">
                                    <input type="text" name="kana" value="<?php echo $data['kana']; ?>" maxlength="100">
                                </div>
                            </div>
                            <div>
                                <div class="label mail">メールアドレス<span>*</span></div>
                                <div class="input">
                                    <input type="text" name="mail" value="<?php echo $data['mail']; ?>" maxlength="100">
                                </div>
                            </div>
                            <div>
                                <div class="label">電話番号<span>*</span></div>
                                <div class="input">
                                    <input type="text" name="tel" value="<?php echo $data['tel']; ?>" maxlength="20">
                                </div>
                            </div>
                            <div>
                                <div class="label">生年月日<span>*</span></div>
                                <div class="input birthday">
                                    <input type="text" name="year" value="<?php echo $data['year']; ?>" maxlength="4"><span>年</span>
                                    <input type="text" name="month" value="<?php echo $data['month']; ?>" maxlength="2"><span>月</span>
                                    <input type="text" name="day" value="<?php echo $data['day']; ?>" maxlength="2"><span>日</span>
                                </div>
                            </div>
                            <div class="boder-none">
                                <div class="label boder-none">備考欄<span>*</span></div>
                                <div class="input boder-none">
                                    <textarea name="message" value="" rows="5" cols="10" maxlength="500"><?php echo $data['message']; ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="conf">
                        <label for="confirmation">
                    <?php if (isset($data['confirmation']) && $data['confirmation'] == 1): ?> 
                        <input type="checkbox" name="confirmation" id="confirmation" value="1" checked="checked">
                    <?php else: ?>
                        <input type="checkbox" name="confirmation" id="confirmation" value="1" >
                    <?php endif; ?> 
                        入力内容をご確認の上、お間違いなければチェックを入れてください</label>
                    </div>
                    <div class="contact-form-btn">
                        <div class="contact-btn">
                            <input type="submit" name="submit">
                        </div>
                    </div>
                </form>
            </div> <!-- end col-full -->
        </div> 
        <!-- end about-content -->

    </section> <!-- end s-about -->

    <!-- footer
    ================================================== -->
    <footer class="s-footer">

        <div class="row footer-main">

            <div class="logo">
                <img src="../images/logo.png" width="50" alt="エスリールのロゴ">
            </div>

        </div> <!-- end footer-main -->

        <div class="row footer-bottom">

            <div class="menu">
                <ul>
                    <li><a href="/">TOP</a></li>
                    <li><a href="/company/">会社概要</a></li>
                    <li><a class="smoothscroll" href="#top">お問い合わせ</a></li>
                </ul>
            </div>

            <div class="col-twelve">
                <div class="go-top">
                    <a class="smoothscroll" title="Back to Top" href="#top"><i class="icon-arrow-up" aria-hidden="true"></i></a>
                </div>
            </div>

        </div> <!-- end footer-bottom -->

        <div class="footer-copyright">
            <div>Copyright © 2024 s-reel</div>
        </div>

    </footer> <!-- end footer -->




    <!-- preloader
    ================================================== -->
    <div id="preloader">
        <div id="loader">
            <img id="img_loading" src="../images/preloader.gif" alt="" class="" width="60px">
        </div>
    </div>


    <!-- Java Script
    ================================================== -->
    <script src="../js/jquery-3.2.1.min.js"></script>
    <script src="../js/plugins.js"></script>
    <script src="../js/main.js"></script>

</body>

</html>