<?php
$result  = '';
$result3 = '';
$result3 = 'です。';
switch (rand(0, 9)) {
    case 0:
    case 1:
    case 2:
        $result = '凶';
    	$result2 = 'ごめんなさい。';
        break;
    case 3:
    case 4:
    case 5:
    case 6:
    case 7:
        $result = '吉';
        break;
    case 8:
    case 9:
    default:
        $result = '大吉';
    	$result2 = 'おめでとうございます！';
    	$result3 = 'です！'; 
        break;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <title>test</title>
</head>
<body>
<p><?php echo $result2; ?>あなたの今日の運勢は<b><?php echo $result; ?></b><?php echo $result3; ?></p>
</body>
</html>