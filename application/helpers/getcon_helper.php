<?php
/**
 * 获取curl函数
 * @param unknown $url
 * @param unknown $data
 * @return string
 */
function getContent($url,$data=array()){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off'))
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
	curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
	curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
	curl_setopt($ch, CURLOPT_TIMEOUT, 100);

	$str = curl_exec($ch);
	$str = iconv('gbk','utf-8',$str);
	$intReturnCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	if($intReturnCode == '200'){
		//echo "HTTP_CODE:".$intReturnCode."\n";
		return $str;
	}
}