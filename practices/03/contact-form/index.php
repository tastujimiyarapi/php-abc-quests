<?php
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

        //エラーメッセージがある場合
        if(!isset($messages) || count($messages) > 0){
            $css_ms = true;
        } else {
            //ここでメール送信処理を行う。
            if(true){
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
    <title>test</title>
    <style type="text/css">
    table{
        width:1000px;
    }
    table tr{
        height:35px;
    }
    table .item{
        width :110px;
        font-size: 15px;
        font-weight :bold ;
    }
    .ok{
        color:red;
    }
    </style>
</head>
<body>
<p class="fm"><B>問い合わせフォーム</B></p>
<form action="index.php" method="POST">
<table>
<?php foreach ($messages as $message){ ?>
    <tr>
        <td class="<?php $css_ms ? ng : ok ;?>" colspan ="2" border=1 ><?= h($message); ?></td>
    </tr>
<?php } ?>
    <tr>
        <td class ="item">*お名前</td>
        <td><input type="text" name="name" value="<?= h($_POST['name']) ?>" placeholder="例）山田 太郎"/></td>
    </tr>
    <tr>
        <td class ="item">*メールアドレス</td>
        <td><input type="text" name="email" value="<?= h($_POST['email']) ?>" placeholder="例）email@exsample.com"/></td>
    </tr>
    <tr>
        <td class ="item">お電話番号</td>
        <td><input type="text" name="tel" value="<?= h($_POST['tel']) ?>" placeholder="例）090-1234-5678"/></td>
    </tr>
    <tr>
        <td class ="item">*ご質問内容</td>
        <td><textarea name="contents" placeholder="ご自由にお書きください"><?= h($_POST['contents']) ?></textarea></td>
    </tr>
    <tr>
        <td><input type="submit" value="送信"></td>
        <td><input type="reset" value="リセット"></td>
    </tr>
</table>
<input type="hidden" name="csrf_key" value="<?=generateCsrfKey(); ?>">
</form>
<p></p>
</body>
</html>
<?php
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