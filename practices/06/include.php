<?php
$pgntn_cnt;
$pgntn_limit;
$pgntn_page;
$pgntn_csrfKey;

//ページネーションの設定
function setPageNation($cnt,$limit,$page,$csrfKey){
    global $pgntn_cnt;
    global $pgntn_limit;
    global $pgntn_page;
    global $pgntn_csrfKey;
    
    $pgntn_cnt      = $cnt;
    $pgntn_limit    = $limit;
    $pgntn_page     = $page;
    $pgntn_csrfKey  = $csrfKey;
}


//XSS対策
function h($str)
{
    return htmlspecialchars($str, ENT_QUOTES);
}
//CSRFの対策
function checkCsrfKey($key)
{
    if (!isset($key) || !isset($_SESSION['csrf_key']) || $_SESSION['csrf_key'] !== $key) {
        echo '不正なアクセスです';
        exit();
    }
}
//トークンの生成
function generateCsrfKey()
{
    return $_SESSION['csrf_key'] = sha1(uniqid(mt_rand(), true));
}
?>