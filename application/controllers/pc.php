<?php
/**
 * 电脑、办公 1级分类
 * url：http://list.jd.com/670-671-672-0-0-0-0-0-0-0-1-1-1-1.html
 * -笔记本		672
 * -超极本		6864
 * -上网本		1105
 * -平板电脑		2694
 * -平板电脑配件	5146
 * -台式机		673
 * -服务器		674
 * -笔记本配件	675
 */
class Pc extends CI_Controller{
	
	
	public function __construct(){
		parent::__construct();
		$this->load->model('dbs');
	}
	
	public function get_brands_detail($gid,$pid,$class_id = 0){
		header("Content-type: text/html; charset=utf-8");
		//$class_id	= 1300; 737-794-798
		
		//$class_id	= 798;
		$url 		= 'http://list.jd.com/'.$gid.'-'.$pid.'-'.$class_id.'.html';
		$contents	= $this->curl->simple_get($url);
// 		preg_match_all('|<div rel=\'.*?\'><a id=\'.*?\' href=\'737-1276-'.$class_id.'-([^-]+)-0-0-0-0-0-0-1-1-1-1.html\' >([^<]+)(（.*?）)+</a></div>|is', $contents, $matches);
// 		preg_match_all('|<div rel=\'.*?\'><a id=\'.*?\' href=\'737-1276-'.$class_id.'-([^-]+)-0-0-0-0-0-0-1-1-1-1.html\' >([a-zA-Z0-9 ]+)</a></div>|is', $contents, $matches2);
		
		//737-1276-967-0-0-6747-0-0-0-0-1-1-1-1.html 737-1276-963-6719-0-0-0-0-0-0-1-1-1-1.html
		
// 		preg_match_all('|<div rel=\'.*?\'><a id=\'.*?\' href=\'737-1277-'.$class_id.'-([^-]+)-0-0-0-0-0-0-1-1-1-1.html\' >([^<]+)(（.*?）)+</a></div>|is', $contents, $matches);
// 		preg_match_all('|<div rel=\'.*?\'><a id=\'.*?\' href=\'737-1277-'.$class_id.'-([^-]+)-0-0-0-0-0-0-1-1-1-1.html\' >([a-zA-Z0-9 ]+)</a></div>|is', $contents, $matches2);
		//<div rel='4'><a id='200957' href='1316-1384-1405-200957-0-0-0-0-0-0-1-1-1-1.html' >乐力</a></div>
		preg_match_all('|<div rel=\'.*?\'><a id=\'.*?\' href=\''.$gid.'-'.$pid.'-'.$class_id.'-0-0-([^-]+)-0-0-0-0-1-1-1-1.html\' >([^<]+)</a></div>|is', $contents, $matches);
		preg_match_all('|<div rel=\'.*?\'><a id=\'.*?\' href=\''.$gid.'-'.$pid.'-'.$class_id.'-0-0-([^-]+)-0-0-0-0-1-1-1-1.html\' >([a-zA-Z0-9 ]+)</a></div>|is', $contents, $matches2);
		
		//print_r($matches);
		if (!empty($matches)) {
			
			if (!empty($matches[2])) {
				$match[1]	= array_merge($matches[1] , $matches2[1]);
				$match[2]	= array_merge($matches[2] , $matches2[2]);
			}else{
				$match	= $matches;
			}
			print_r($match);
			foreach ($match[1] as $key => $val) {
				$data['brand_id']	= $val;
				$data['brand_name'] = $match[2][$key];
				$data['c1']			= 737;
				$data['c2']   		= 1277;
				$data['c3'] 		= $class_id;
				$this->dbs->ins_brands($data , 'brands');
			}
			
		}
		//print_r($data);
	}

	public function get_brands(){
		header("Content-type: text/html; charset=utf-8");
		
		
		$i = 0;
		while ($i >= 0){
			$res	= $this->dbs->get_db('' , 'computer' , $i);
		//	print_r($res);exit;
			
			if (!empty($res)) {
					
				foreach ($res as $k => $val){
					$jd_id = $val['jd_id'];
					$url	= 'http://item.jd.com/'.$jd_id.'.html';
					$contents	= $this->curl->simple_get($url);
					preg_match_all('|<a href="http://www.jd.com/brands/([^-]+)-([^.]+).html" >([^<]+)</a>&nbsp;&gt;&nbsp;<a href="http://item.jd.com/.*?.html" >([^<]+)</a>|is', $contents , $brands);
					if (!empty($brands) && isset($brands[1])) {
						$up_info	= array(
								'brand_id'		=> $brands[2][0],
								'brand_name'	=> $brands[3][0],
								'name'			=> $brands[4][0],
						);
						
						$this->dbs->up_data('computer' , $up_info , $jd_id);
						echo $val['id'] . "\n";
					}
				}
				$i		= $i +100;
			}else{
				$i = -1;
			}
			
		}
		//print_r($brands);
		
	}
	/**
	 * 获取电脑、办公三级分类	[http://channel.jd.com/computer.html]
	 * 	电脑、办公
	 * 		电脑整机
	 * 			笔记本
	 * 			超极本
	 */
	public function get_3_class(){
		
		//header("Content-type: text/html; charset=utf-8");
		$url		= 'http://channel.jd.com/computer.html';
		//$this->curl->proxy('173.213.96.229', 8089);
		$classes	= $this->curl->simple_get($url);
		preg_match_all('|<li><a ref="http://list.jd.com/([^-]+)-([^-]+)-([^.]+).html">([^<]+)</a></li>|is', $classes , $arr_class);
		foreach ($arr_class[3] as $k => $val){
			
			$data	= array(
					'class_1'	=> $arr_class[1][$k],
					'class_2'	=> $arr_class[2][$k],
					'class_3'	=> $val,
					'class_name'	=> $arr_class[4][$k],
			);
			$this->dbs->insert_db($data , 'class_3');
		}
		//print_r($arr_class);
	}
	
	public function index(){
		
		//$db_class	= $this->_get_db_class();//获取数据库中分类
		$db_class[0]	= array('class_1' => 737 , 'class_2' => 794 ,'class_3' => 798);
		
		//header("Content-type: text/html; charset=utf-8");
		//$this->curl->proxy('147.46.222.194', 3128);
		//$this->curl->proxy('202.171.253.98', 80);//备用ip1
		//$this->curl->proxy('106.187.43.140', 8080);//备用ip2
		//http://list.jd.com/737-794-798-0-0-0-0-0-0-0-1-1-1-1-1-72-33.html
		foreach ($db_class as $ck => $cv){
	 		$i = 1;
	 		while ($i > 0){
	 			echo 'current pagenum i =====>' . $i."\n";
				$url	= 'http://list.jd.com/'.$cv['class_1'].'-'.$cv['class_2'].'-'.$cv['class_3'].'-0-0-0-0-0-0-0-1-1-'.$i.'-1-1-72-33.html';
				//$url	= 'http://list.jd.com/'.$cv['class_1'].'-'.$cv['class_2'].'-'.$cv['class_3'].'-0-0-0-0-0-0-0-1-1-'.$i.'-1.html';
				echo 'curren url ======>' . $url . "\n";
				$instro	= $this->curl->simple_get($url);
				//print_r($instro);
				preg_match_all('|<div class=\'p-img\'><a target=\'_blank\'  href=\'http://item.jd.com/([^.]+).html\'><img width=\'220\' height=\'220\'|is', $instro , $ids);
				print_r($ids);
				if ( empty($ids[0])) {
					$i = -1;
				}else{
					$i++;
					foreach ($ids[1] as $k => $id){
						$this->get_detail($id , $cv);
						echo $id . "\n";
					}
				}
	 		}
 		}
	}
	/* private function _change_proxy($i = 1){
		
		switch ($i){
			case 1:
				echo 1;
				$this->curl->proxy('147.46.222.194', 31281);
				break;
			case 2:
				echo 2;
				$this->curl->proxy('202.171.253.98', 80);
				break;
			case 3:
				$this->curl->proxy('106.187.43.140', 8080);
				break;
		}
		
	} */
	
	/**
	 * 获取分类
	 * @return unknown
	 */
	private function _get_db_class(){
		
		$res	= $this->dbs->get_db('' , 'class_3');
		return $res;
	}
	/**
	 * 获取商品详情数据并入库
	 * @param number $jd_id
	 * @return boolean
	 */
	public function get_detail($jd_id = 0 , $class_arr){
		
		if ($jd_id < 0) {
			return false;
		}
		$thumb		= $this->_get_tumb_pic($jd_id);//无水印缩略图
		$title		= $this->_get_title($jd_id);//获取商品标题
		$classes	= $this->_get_classes($jd_id);
		$intros		= $this->_get_intros($jd_id);//序列化过的商品简介
		$params		= $this->_get_params($jd_id);//序列化过的商品属性
		//print_r($classes);
		$data		= array(
				
				'jd_id'		=> $jd_id,
				'thumb'		=> $thumb,
				'title'		=> $title[1],
				'title_1'	=> isset($title[2]) ? $title[2] : '',
				'class_1'	=> $class_arr['class_1'],
				'class_2'	=> $class_arr['class_2'],
				'class_3'	=> $class_arr['class_3'],
				'brand'		=> $classes[4],
				'intros'	=> $intros,
				'params'	=> $params,
		);
		$this->dbs->insert_db($data,'jiadian2');
		
	}
	
	private function _get_params($jd_id = 0){
		
		$cont	= $this->_get_contents($jd_id);
		preg_match_all('|<div class="mc  hide" data-widget="tab-content" id="product-detail-2">(.*?)</div>|is', $cont , $params);
		if (!empty($params)) {
			$param	= str_replace(' class="tdTitle"', '', $params[1][0]);
			return serialize(trim($param));
		}else{
			return '';
		}
	}
	/**
	 * 获取商品简介属性
	 * @param number $jd_id
	 * @return string
	 */
	private function _get_intros($jd_id = 0){
		
		$cont	= $this->_get_contents($jd_id);
		preg_match_all('|<ul class="detail-list">(.*?)</ul>|is', $cont , $intros);
		if (!empty($intros)) {
			$intro	= $this->_replace_a($intros[1][0]);
			$intro	= '<ul>' . trim($intro) . '</ul>';
			return serialize($intro);
		}else{
			return '';
		}
	}
	/**
	 * 去掉字符串中所有的a链接
	 * @param string $string
	 * @return string
	 */
	private function _replace_a($string = ''){
		
		$str	= strip_tags($string , '<ul> </ul> <li> </li>');
		return $str;
	}
	/**
	 * 获取分类及品牌 前3为分类，4为品牌
	 * @param number $jd_id
	 * @return multitype:NULL |multitype:
	 */
	private function _get_classes($jd_id = 0){
		
		$cont	= $this->_get_contents($jd_id);
		preg_match_all('|<div class="breadcrumb">.*?<strong><a href=".*?">([^<]+)</a></strong><span>&nbsp;&gt;&nbsp;<a href=".*?" >([^<]+)</a>&nbsp;&gt;&nbsp;<a href=".*?" >([^<]+)</a>&nbsp;&gt;&nbsp;<a href=".*?" >([^<]+)</a>.*?</span>|is',$cont,$classes);
		if(!empty($classes)){
			$data	= array(
					1	=> trim($classes[1][0]),//1级
					2	=> trim($classes[2][0]),//2级
					3	=> trim($classes[3][0]),//3级
					4	=> trim($classes[4][0]),//品牌
			);
			return $data;
		}else{
			return array();
		}
	}
	/**
	 * 获取商品标题
	 * @param number $jd_id
	 * @return boolean|Ambigous <>|string
	 */
	public function _get_title($jd_id = 0){
		
		$cont	= $this->_get_contents($jd_id);
		preg_match_all('|<div id="name">.*?<h1>([^<]+)</h1>|is', $cont , $titles);
		if(!empty($titles) && isset($titles[1][0])){
			$title	= explode(' ', $titles[1][0]);
			$titles	= array(1 => $title[0] , 2=> $title[1]);
			if (!empty($titles)) {
				return $titles;
			}else{
				return array();
			}
		}
		return '';
	}
	/**
	 * 获取缩略图 无水印
	 * @param unknown $jd_id
	 * @return boolean|string
	 */
	public function _get_tumb_pic($jd_id){
		
		$cont	= $this->_get_contents($jd_id);
		preg_match('|<div id="preview">.*?src="(.*?)".*?>|is', $cont , $tumb);
		if (!empty($tumb)) {
			return $this->down($tumb[1],2);
			//return $tumb[1];
		}else{
			return '';
		}
	}
	/**
	 * 获取详情页内容
	 * @param number $jd_id
	 * @return boolean|unknown
	 */
	private function _get_contents($jd_id = 0){
		if ($jd_id < 0) {
			return false;
		}
		$this->curl->create('http://item.jd.com/');
		$url	= 'http://item.jd.com/'.$jd_id.'.html';
		$cont	= $this->curl->simple_get($url);
		if (empty($cont)) {
			return false;
		}else {
			return $cont;
		}
	}
	/**
	 * 下载图片
	 * @param unknown $url
	 * @param number $type
	 * @param string $wenjianjia
	 * @return string
	 */
	function down($url,$type = 1,$wenjianjia=''){
		ini_set('display_errors',true);//Just in case we get some errors, let us know….
		$host = "www.jd.com";
		$i = 1;
		if ($type == 1){
			$save_to='e:/uping/goods/'.$wenjianjia;
		}else{
			$save_to='e:/uping/goods/jiadian2/';//缩略图地址
		}
			
		if(!file_exists($save_to)){
			echo $save_to;
			mkdir($save_to);//创建当前日期的目录
		}
		$mh = curl_multi_init();
		//http://img11.360buyimg.com/n1/g10/M00/0C/0A/rBEQWFFINpEIAAAAAAENu92M7ikAACaEAEAr2sAAQ3T536.jpg
		//preg_match_all('|.*?product/.*?/(.*)?|is', dirname($url), $path_add);
		$rang2		= mt_rand(10000,99999);
		$lastpix	= base_convert($rang2,10,36);
		$pic_name	= substr(basename($url) , -20);
		
		$g=$save_to.$lastpix.$pic_name;
		if(!is_file($g)){
			$conn[$i]=curl_init($url);
			$fp[$i]=fopen ($g, "wb");
			curl_setopt($conn[$i], CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.0; zh-CN; rv:1.9.0.1) Gecko/2008070208 Firefox/3.0.1");
			curl_setopt($conn[$i], CURLOPT_REFERER, "http://$host");
			curl_setopt($conn[$i], CURLOPT_FILE, $fp[$i]);
			curl_setopt($conn[$i], CURLOPT_HEADER ,0);
			curl_setopt($conn[$i], CURLOPT_CONNECTTIMEOUT,600);
			//curl_setopt($conn[$i], CURLOPT_FOLLOWLOCATION, 1);
			//curl_setopt($conn[$i], CURLOPT_RETURNTRANSFER, 0);
			//curl_setopt($conn[$i], CURLOPT_VERBOSE, 0);
			curl_multi_add_handle ($mh,$conn[$i]);
		}
		do {
			$n=curl_multi_exec($mh,$active);
		}while ($active);
		curl_multi_remove_handle($mh,$conn[$i]);
		curl_close($conn[$i]);
		fclose ($fp[$i]);
		curl_multi_close($mh);
		$img	= substr($g,9);//f:/bigpic/goods/notebook/734ceMtHW5i5Ao2I.jpg
		return $img;
	}
	
	
	
	
	
	
	
	
	
	
}