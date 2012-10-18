<?php

$p = json_decode($_POST['p']);
$p_a = get_object_vars($p);

if (empty($p->url)) {
    echo "Error Url";
    die;
}

if (strpos($p->url, "http://") === false) {
    echo "Error Request";
    die;
}

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $p->url);
// 设置header显示
curl_setopt($curl, CURLOPT_HEADER, 1);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 0);
if (isset($p_a['user-agent'])) {
    curl_setopt($curl, CURLOPT_USERAGENT, $p_a['user-agent']);
}
if (isset($p_a['referer'])) {
    curl_setopt($curl, CURLOPT_REFERER, $p_a['referer']);
}
if (isset($p->cookie)) {
    curl_setopt($curl, CURLOPT_COOKIE, $p->cookie);
}
curl_setopt($curl, CURLOPT_TIMEOUT, 30);

// 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
if ($p->reqData != null) {
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $p->reqData);
}
// 获取http头
// 运行cURL，请求网页
$data = curl_exec($curl);
$info = curl_getinfo($curl);
// 关闭URL请求
curl_close($curl);
$h_pos = -1;
$ext = -1;
if ($data == false) {
    echo "Time Out";
    die;
}

if (($tmp_pos = strpos($data, "\r\n\r\n")) !== false) {
    $h_pos = $tmp_pos;
    $ext = 4;
} else {
    if (($tmp_pos = strpos($data, "\n\n")) !== false) {
        $h_pos = $tmp_pos;
        $ext = 2;
    }
}
if ($h_pos == -1) {
    echo "错误的结果啊";
    die;
}
$head_data = substr($data, 0, $h_pos + $ext);
preg_match("/Location:(.*)/", $head_data, $match);
if (isset($match[1])) {
    $info["redirect_url"] = trim($match[1]);
    header("Location:{$info["redirect_url"]}");
    die;
}
header("Content-Type:{$info['content_type']}");
// 过滤出set_cookie
preg_match_all("/Set-Cookie:(.*?);/", $head_data, $match, PREG_SET_ORDER);
foreach ($match as $r) {
    header("Set-Cookie:{$r[1]}");
}
$body_data = substr($data, $h_pos + $ext);
echo $body_data;

