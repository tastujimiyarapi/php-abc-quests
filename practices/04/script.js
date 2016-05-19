$(function () {
	//ラジオボタンイベント
	$('input[class=yRadio]').change( function(){
		if ($('input[class=yRadio]:checked').val() == 0) {
			//ご意見内容の場合
			$('.for-question').hide();
			$('.for-opinion').show();
		}
		else {
			//ご質問内容の内容
			$('.for-opinion').hide();
			$('.for-question').show();
		}
	});
});

function disp(){
	// 「OK」時の処理開始 ＋ 確認ダイアログの表示
	if(window.confirm('送信しますか？')){

		location.href = "index.php"; // example_confirm.html へジャンプ

	}
}