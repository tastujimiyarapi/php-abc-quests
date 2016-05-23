<?php

// 設定ファイルを読み込み.
$settings = require __DIR__ . '/../secret-settings.php';

//GETから検索条件を取得
$name = isset($_GET['name'])? trim($_GET['name']) : '';
$tel  = isset($_GET['tel'] )? trim($_GET['tel'])  : '';

//入力されていない。場合は、検索画面にリダイレクト
if(empty($name) && empty($tel)){
	header('location: search.php?error=1');
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
    
    //セレクト文の作成
    $sql_select  = "SELECT * FROM inputform WHERE ";
    
    if(isset($name) && !empty($name)){ $where[] = 'name=:name';}
    if(isset($tel) && !empty($tel)){ $where[] = 'tel=:tel';}
    
    $sql_select .= implode(' AND ',$where);
    
    echo $sql_select;
    try{
        
        if ($stmt = $dbh->prepare($sql_select)) {
        	if(isset($name) && !empty($name)){$stmt->bindParam(':name', $name, PDO::PARAM_STR);}
        	if(isset($tel) && !empty($tel)){ $stmt->bindParam(':tel', $tel, PDO::PARAM_STR);}
        }
        
        $stmt->execute();
        
    } catch(Exception $e){
        echo "実行できない。".$e->getCode();
    }
    
    //dbクローズ
    $dbh = NULL;
    
    //検索結果が0件の場合は、エラーで検索画面へ
    if($stmt->rowCount() < 1){
    	header('location: search.php?error=2');
    	exit();
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
    <title>入力データ一覧</title>
</head>
<body>
	<nav class="navbar navbar-default navbar-static-top">
    	<div class="container">
    		<div class="navbar-header">
    			<a class="navbar-brand" href=".">Sample Web Site</a>
    		</div>
    	</div>
    </nav>
    
    <div id="page">	
        <div class="container">
        	<h1>入力データ一覧</h1>
        	<div class="row">
        	    <div class="col-sm-9">
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
                                <td><?php print(($result['id'])); ?></td>
                                <td><?php print($result['flag'] == 0 ? "ご意見":"ご質問"); ?></td>
                                <td><?php print($result['name']); ?></td>
                                <td><?php print($result['email']); ?></td>
                                <td><?php print($result['tel']); ?></td>
                                <td><?php print($result['content']); ?></td>
                            </tr>
<?php } ?> 
                        </tbody>
                    </table>
        	   </div>
        	</div>
        </div><!-- /container -->
    </div><!-- /page -->
</body>
</html>