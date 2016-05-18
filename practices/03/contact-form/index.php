<?php
// 設定ファイルを読み込み.
$settings = require __DIR__ . '/../../secret-settings.php';

//セッションのスタート
session_start();

$messages = array();

switch(strtolower($_SERVER["REQUEST_METHOD"])){
    case 'get':
        //ゲットの場合はなにもなし。
        break;
    case 'post':
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
            
            //本文作成
            $mailtmp = <<< EOF

------------------------------------------------------------
お名前：{$_POST['name']}
------------------------------------------------------------
メールアドレス：{$_POST['email']}
------------------------------------------------------------
お電話番号：{$_POST['tel']}
------------------------------------------------------------
ご質問内容：
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
<html>
<head>
    <meta charset="UTF-8"/>
    <title>問い合わせフォーム</title>
        <style type="text/css">
        table {
            border-collapse: collapse;
        }
        table thead tr td.success {
            border: 1px solid #6c6;
            background-color: #dfd;
            padding: 5px 10px;
        }
        table thead tr td.error {
            border: 1px solid #c66;
            background-color: #fdd;
            padding: 5px 10px;
            color: #f00;
        }
        table tbody tr th,
        table tbody tr td {
            border: none;
            padding: 10px;
        }
        table tbody tr td {
            width: 300px;
        }
        table tbody tr th {
            text-align: left;
            vertical-align: top;
        }
        table tbody tr th span {
            color: #f00;
        }
        table tbody tr td input,
        table tbody tr td textarea {
            width: 100%;
        }
        table tfoot tr td {
            text-align: right;
        }
    </style>
</head>
<body>
<h1>問い合わせフォーム</h1>
<form action="index.php" method="POST">
<table>
    <thead>
<?php foreach ($messages as $message){ ?>
    <tr>
        <td class="<?php if($css_ms){ echo "error";} else { echo "success";} ;?>" colspan ="2" border=1 ><?= h($message); ?></td>
    </tr>
<?php } ?>
    </thead>
    <tbody>
    <tr>
        <th><span>*</span> お名前</th>
        <td><input type="text" name="name" value="<?= h($_POST['name']) ?>" placeholder="例）山田 太郎"/></td>
    </tr>
    <tr>
        <th><span>*</span> メールアドレス</th>
        <td><input type="text" name="email" value="<?= h($_POST['email']) ?>" placeholder="例）email@exsample.com"/></td>
    </tr>
    <tr>
        <th>お電話番号</th>
        <td><input type="text" name="tel" value="<?= h($_POST['tel']) ?>" placeholder="例）090-1234-5678"/></td>
    </tr>
    <tr>
        <th><span>*</span> ご質問内容</th>
        <td><textarea name="contents" placeholder="ご自由にお書きください"><?= h($_POST['contents']) ?></textarea></td>
    </tr>
    </tbody>
    <tfoot>
        <td colspan="2">
            <input type="hidden" name="csrf_key" value="<?=generateCsrfKey(); ?>">
            <input type="submit" value="送信">
            <input type="reset" value="リセット">
        </td>
    </tfoot>
</table>
</form>
</body>
</html>
<?php
//メール本文作成
function mailText(){
}


//入力項目のチェックを行う。
function validate($post, &$messages) {
    //必須チェック(名前、アドレス、電話番号、質問内容)
    hissuCheck($post['name'],$messages,"お名前");
    hissuCheck($post['email'],$messages,"メールアドレス");
    hissuCheck($post['contents'],$messages,"ご質問内容");
    
    //メールアドレスの内容チェック
    is_mail($post['email'], $messages);
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