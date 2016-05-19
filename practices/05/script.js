$(function () {
	
	//ロード時の項目表示設定
	stateRadio();
	
	//送信ボタンイベント
	$('#contact-form').on('submit', function(){
		return confirm('送信しますか？');
	});
	
	//ラジオボタンイベント
	$('#contact-form input[class=yRadio]').on('change', function(){
		stateRadio();
	});
});

//ラジオボタンの状態による設定
function　stateRadio(){
	if ($('input[class=yRadio]:checked').val() == 0) {
		//ご意見内容の場合
		clickOpinion();
	} else {
		//ご質問内容の内容
		clickQuestion();
	}
}
//ご意見選択時の表示切替
function clickOpinion(){
	$('#tel').hide();
	$('#question').hide();
	$('#opinion').show();
}
//ご質問選択時の表示切替
function clickQuestion(){
	$('#tel').show();
	$('#opinion').hide();
	$('#question').show();
}