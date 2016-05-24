<?php

include 'include.php';

define(MAX_LIMIT,10);

// 設定ファイルを読み込み.
$settings = require __DIR__ . '/../secret-settings.php';

//セッションのスタート
session_start();

//CSRF対策
checkCsrfKey($_GET['csrf_key']);

//検索条件をセッションに格納
if(!isset($_SESSION['search'])){
    //セッションにセットされていない場合は、セット
    $_SESSION['search'] = array(
        'name'  => isset($_GET['name'] )? trim($_GET['name'])  : '',
        'tel'   => isset($_GET['tel'] )? trim($_GET['tel'])  : '',
        'error' => 0,
    );
}
//GETから取得
$page = isset($_GET['page'])? $_GET['page'] : 1;

$name = $_SESSION['search']['name'];
$tel  = $_SESSION['search']['tel'];

//入力されていない。場合は、検索画面にリダイレクト
if(empty($name) && empty($tel)){
	header('location: search.php?');
	$_SESSION['search']['error'] = 1;
	exit();
}

try{
    // Create connection
    $dbh = new PDO($settings['dbh'], $settings['username'], $settings['password']);
}catch(PODException $e){
    die('Connect Error:' . $e->getCode());
}

$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

//検索条件を作成
if(isset($name) && !empty($name)){ $where[] = 'name=:name';}
if(isset($tel) && !empty($tel)){ $where[] = 'tel=:tel';}

//件数の取得----------------------------------------------------------
$sql_count  = "SELECT COUNT(*) FROM inputform WHERE ";
$sql_count .= implode(' AND ',$where);

try{

    if ($stmt = $dbh->prepare($sql_count)) {
    	if( isset($name) && !empty($name)){ $stmt->bindParam(':name', h($name), PDO::PARAM_STR);}
        if( isset($tel)  && !empty($tel)){ $stmt->bindParam(':tel', h($tel), PDO::PARAM_STR);}
    }
    
    $stmt->execute();
        
} catch(Exception $e){
    echo "件数取得が実行できない。：".$e->getCode();
    exit();
}

$cnt = $stmt->fetchColumn();

//検索結果が0件の場合は、エラーで検索画面へ
if($cnt < 1){
	header('location: search.php');
	$_SESSION['search']['error'] = 2;
	exit();
}

$stmt = NULL;

//データの取得-------------------------------------------------------
$sql_select  = "SELECT * FROM inputform WHERE ";
$sql_select .= implode(' AND ',$where);
$sql_select .= " ORDER BY id LIMIT " . MAX_LIMIT . " OFFSET " . MAX_LIMIT*($page - 1);
    
try{

    if ($stmt = $dbh->prepare($sql_select)) {
    	if( isset($name) && !empty($name) ){ $stmt->bindParam(':name', h($name), PDO::PARAM_STR);}
        if( isset($tel)  && !empty($tel) ){ $stmt->bindParam(':tel', h($tel), PDO::PARAM_STR);}
    }
    
    $stmt->execute();
        
} catch(Exception $e){
    echo "データ取得が実行できない。：".$e->getCode();
    exit();
}

//DBクローズ
$dbh = NULL;

//トークンの生成
$csrfKey = isset($_SESSION['csrf_key'])? $_SESSION['csrf_key'] : generateCsrfKey();

//ページネーションの設定
setPageNation($cnt,MAX_LIMIT,$page,$csrfKey);
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
    <title>入力データ一覧</title>
</head>
<body>
	<nav class="navbar navbar-default navbar-static-top">
    	<div class="container">
    		<div class="navbar-header">
    			<a class="navbar-brand" href="list.php?page=1&csrf_key=<?php echo $csrfKey?>">入力データ一覧</a>
    		</div>
    	</div>
    </nav>
    	
        <div class="container">
        	<div class="row">
				<!-- pagenation -->
<?php include 'pagenation.php'; ?>
				<!-- pagenation -->
        		<div class="col-sm-12">
        	        <table class="table  table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>種別</th>
                                <th>お名前</th>
                                <th>メールアドレス</th>
                                <th>お電話番号</th>
                                <th>ご質問内容</th>
                            </tr>
                        </thead>
                        <tbody>
<?php while($result = $stmt->fetch(PDO::FETCH_ASSOC)){ ?>
                            <tr>
                                <td><?php print(h(sprintf('%05d', $result['id']))); ?></td>
                                <td><?php print(h($result['flag'] == 0 ? "ご意見":"ご質問")); ?></td>
                                <td><?php print(h($result['name'])); ?></td>
                                <td><?php print(h($result['email'])); ?></td>
                                <td><?php print(h($result['tel'])); ?></td>
                                <td><?php print(nl2br((h($result['content'])))); ?></td>
                            </tr>
<?php } ?> 
                        </tbody>
                    </table>
				</div>
				<!-- pagenation -->
<?php include 'pagenation.php'; ?>
				<!-- pagenation -->
        	</div>
        </div><!-- /container -->
</body>
</html>