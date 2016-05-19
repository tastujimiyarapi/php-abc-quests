<?php
// 設定ファイルを読み込み.
$settings = require __DIR__ . '/../secret-settings.php';
//セッションのスタート
session_start();

$messages = array();

switch(strtolower($_SERVER["REQUEST_METHOD"])){
    case 'get':
        $isGet = 1;
        break;
    case 'post':
    	$isGet = 0;
        //CSRF対策
        if (!isset($_POST['csrf_key']) || !checkCsrfKey($_POST['csrf_key'])) {
            echo '不正なアクセスです';
            exit;
        }
        
        //項目チェック
        validate($_POST, $messages);

        if(!isset($messages) || count($messages) > 0){
        	//エラーメッセージがある場合
            $css_ms = true;
        } else {
            //ここでメール送信処理を行う。
            mb_language('Japanese');
            mb_internal_encoding('UTF-8');
            
            //質問、ご意見内容の切替
            $cont = getContentsName();
            //本文作成
            $mailtmp = <<< EOF

------------------------------------------------------------
お名前：{$_POST['name']}
------------------------------------------------------------
メールアドレス：{$_POST['email']}
------------------------------------------------------------
お電話番号：{$_POST['tel']}
------------------------------------------------------------
{$cont}：
{$_POST['contents']}
------------------------------------------------------------

EOF;
			//Webサイト管理者宛て
			$mailtx1 = <<< EOF
以下のお客様からお問い合わせがありました。
{$mailtmp}
EOF;
			//ユーザー宛て
			$mailtx2 = <<< EOF
以下の内容でお問い合わせを受け付けました。
担当者より折り返しご連絡を差し上げますので、今しばらくお待ちください。
{$mailtmp}
EOF;

			//メール送信処理
            if((mb_send_mail($settings['email'], h('お問い合わせがありました'), $mailtx1, 'From: ' . mb_encode_mimeheader('テスト') . ' <no-reply@example.com>'))
            		&& (mb_send_mail($_POST['email'], h('お問い合わせがありがとうございました'), $mailtx2, 'From: ' . mb_encode_mimeheader('テスト') . ' <no-reply@example.com>'))){
                $css_ms = false;
                //送信しましたのメッセージを表示する。
                $messages[] = "メールの送信が完了しました。";
                $_POST = NULL;
            } else {
                //送信に失敗した場合。
                $css_ms = true;
                $messages[] = "メールの送信に失敗しました。";
            }
        }
        
        break;
    default:
        echo 'こんなエラー起こるのか？';
}


?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="//oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script type="text/javascript" src="script.js"></script>
    <title>問い合わせフォーム</title>
</head>
<body>
   <div id="page">
    <div class="container">
      <h1>問い合わせフォーム</h1>
      <div class="row">
        <div class="col-sm-9">
<?php foreach ($messages as $message){ ?>
        <div class="<?php if($css_ms){ echo "alert alert-danger";} else { echo "alert alert-success";} ;?>" role="alert"><?= h($message); ?></div>
<?php } ?>
          <form id="contact-form" action="index.php" method="POST" class="form-horizontal">
             <div class="form-group">
              <label for="input-name" class="col-sm-2 control-label">お問い合わせ種別</label>
        		<div class="radio-inline">
        		 <input type="radio" value="0" name="c" class="yRadio" <?php if($isGet == 1 || $_POST['c'] == 0){echo "checked";} ?>><label for="man">ご意見</label>
        		</div>
 			    <div class="radio-inline">
 				 <input type="radio" value="1" name="c" class="yRadio" <?php if($_POST['c'] == 1){echo "checked";} ?>><label for="woman">ご質問</label>
 				</div>
 			</div>
            <div class="form-group">
              <label for="input-name" class="col-sm-2 control-label"><span style="color: #f00">*</span> お名前</label>
              <div class="col-sm-10">
                <input name="name" type="text" class="form-control" value="<?= h($_POST['name']) ?>" id="input-name" placeholder="例）山田 太郎">
              </div>
            </div>
            <div class="form-group">
              <label for="input-mail" class="col-sm-2 control-label"><span style="color: #f00">*</span> メールアドレス</label>
              <div class="col-sm-10">
                <input name="email" type="text" class="form-control" value="<?= h($_POST['email']) ?>" id="input-mail" placeholder="例）email@exsample.com">
              </div>
            </div>
            <div id="tel" class="form-group">
              <label for="input-mail" class="col-sm-2 control-label">お電話番号</label>
              <div class="col-sm-10">
                <input name="tel" type="text" class="form-control" value="<?= h($_POST['tel']) ?>" id="input-mail" placeholder="例）090-1234-5678">
              </div>
            </div>
            <div class="form-group">
              <label id="question" class="col-sm-2 control-label"><span style="color: #f00">*</span> ご質問内容</label>
        	　<label id="opinion"  class="col-sm-2 control-label"><span style="color: #f00">*</span> ご意見内容</label>
              <div class="col-sm-10">
                <textarea name="contents" class="form-control" rows="5" placeholder="ご自由にお書きください"><?= h($_POST['contents']) ?></textarea>
              </div>
            </div>
            <div class="form-group">
              <div class="col-sm-offset-2 col-sm-10">
              	<input type="hidden" name="csrf_key" value="<?=generateCsrfKey(); ?>">
                <button type="submit" class="btn btn-primary">送信</button>
        		<button type="reset" class="btn btn-default">リセット</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div><!-- /container -->
  </div><!-- /page -->
</body>
</html>
<?php

//入力項目のチェックを行う。
function validate($post, &$messages) {
    //必須チェック(名前、アドレス、電話番号、質問内容)
    hissuCheck($post['name'],$messages,"お名前");
    hissuCheck($post['email'],$messages,"メールアドレス");
    hissuCheck($post['contents'],$messages,getContentsName());
    
    //メールアドレスの内容チェック
    is_mail($post['email'], $messages);
}

//質問内容、ご意見内容により表示切替
function getContentsName(){
	return ($_POST['c'] == 0)? "ご意見内容":"ご質問内容";
}
//メールアドレス妥当性チェック
function is_mail($email, &$messages){
    if (trim($email) !== '' && !preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", trim($email))) {
        $messages[] = "不正なメールアドレスです。";
    }
}

//必須チェック（true：入力している、false:入力してない。）
function hissuCheck($koumoku, &$messages, $koumokumei){
    $koumoku = trim($koumoku);
    if(!isset($koumoku) || $koumoku === ''){
        $messages[] = "{$koumokumei} を入力してください。";
    }
}

//XSS対策
function h($str)
{
    return htmlspecialchars($str, ENT_QUOTES);
}

//トークンの生成
function generateCsrfKey()
{
    return $_SESSION['csrf_key'] = sha1(uniqid(mt_rand(), true));
}

//CSRFの対策
function checkCsrfKey($key)
{
    if (!isset($key) || !isset($_SESSION['csrf_key']) || $_SESSION['csrf_key'] !== $key) {
        return false;
    }
    return true;
}
?>