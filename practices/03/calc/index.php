<?php
if(isset($_GET['operator'])){
	$left = $_GET['left'];
	$right = $_GET['right'];
	$selected = array(
					'-' => false,
					'+' => false,
					'*' => false,
					'/' => false
				);
	switch($_GET['operator']){
		case '-':
			$answer = $_GET['left'] - $_GET['right'];
			$selected['-'] = true;
			break;
		case '+':
			$answer = $_GET['left'] + $_GET['right'];
			$selected['+'] = true;
			break;
		case '*':
			$answer = $_GET['left'] * $_GET['right'];
			$selected['*'] = true;
			break;
		case '/':
			$answer = $_GET['left'] / $_GET['right'];
			$selected['/'] = true;
			break;
	}
	$out =  $left.$_GET['operator'].$right."=".$answer;
} else {
	$selected['+'] = true;
	$out = '計算結果なし';
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <title>test</title>
</head>
<body>
<form action ="index.php" method="GET">
<input type="text" name="left" value="<?= $left ?>" required autofocus/>
<select name="operator">
    <option value="+" <?=$selected['+'] ? "selected ":""; ?>>+</option>
    <option value="-" <?=$selected['-'] ? "selected ":""; ?>>-</option>
    <option value="*" <?=$selected['*'] ? "selected ":""; ?>>*</option>
    <option value="/" <?=$selected['/'] ? "selected ":""; ?>>/</option>
</select>
<input type="text" name="right" value="<?= $right ?>" required/>
<input type="submit" value="計算する">
<form>
<p><?php echo $out; ?></p>
</body>
</html>