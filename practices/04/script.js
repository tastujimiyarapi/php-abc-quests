$(function () {
	//送信ボタンイベント
	$('#contact-form').on('submit', function(){
		return confirm('送信しますか？');
	});
	
	//ラジオボタンイベント
	$('#contact-form input[class=yRadio]').on('change', function(){
		$('#contact-form').find('.for-question, .for-opinion').toggle();
		
//		if ($('input[class=yRadio]:checked').val() == 0) {
//			//ご意見内容の場合
//			$('.for-question').hide();
//			$('.for-opinion').show();
//		}
//		else {
//			//ご質問内容の内容
//			$('.for-opinion').hide();
//			$('.for-question').show();
//		}
	});
});

function disp(){
	// 「OK」時の処理開始 ＋ 確認ダイアログの表示
	if(window.confirm('送信しますか？')){

		location.href = "index.php"; // example_confirm.html へジャンプ

	}
}