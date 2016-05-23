<?php
$err_msg =array(
	1 => "検索条件を入力してください。",
	2 => "検索対象が存在しませんでした。",
	);
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
    <title>検索画面</title>
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
        	<h1>検索</h1>
        	<div class="row">
        	    <div class="col-sm-9">
<?php if(isset($_GET['error'])){ ?>
        <div class="alert alert-danger" role="alert"><?php echo $err_msg[$_GET['error']]; ?></div>
<?php } ?>
    	   			<form  action="list.php" method="GET" class="form-horizontal">
            	       
            	       <div class="form-group">
            	           <label for="input-name" class="col-sm-2 control-label">お名前</label>
            	           <div class="col-sm-10">
            	               <input name="name" type="text" class="form-control" value="" id="input-name" placeholder="例）山田 太郎">
            	           </div>
            	       </div>
            	       
            	       <div class="form-group">
            	           <label for="input-name" class="col-sm-2 control-label">電話番号</label>
            	           <div class="col-sm-10">
            	               <input name="tel" type="text" class="form-control" value="" id="input-name" placeholder="例）090-1234-5678">
            	           </div>    	           
            	       </div>
            	       
            	       <div class="form-group">
                          <div class="col-sm-offset-2 col-sm-10">
                      	    <input type="hidden" name="csrf_key" value="">
                            <button type="submit" class="btn btn-primary">検索</button>
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