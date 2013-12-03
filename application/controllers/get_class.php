<?php
/**
 * 获取京东商品分类
 * @author Ryans
 *
 */
class Get_class extends CI_Controller{
	
	public function __construct(){
	
		parent::__construct();
		$this->load->model('dbs');
	}
	
	/**
	 * 获取化妆品分类
	 */
	public function hzp(){
		
		$url		= 'http://channel.jd.com/beauty.html';
		//$this->curl->proxy('173.213.96.229', 8089);
		$classes	= $this->curl->simple_get($url);
		//preg_match_all('|<a  title=".*?" href="http://www.jd.com/products/([^-]+)-([^-]+)-([^-]+)-0-0-0-0-0-0-0-1-1-1-1-72-33.html">([^<]+)</a>|is', $classes , $arr_class);
		preg_match_all('|<a href="http://list.jd.com/([0-9]+)-([0-9]+)-([0-9]+).html" title=\'.*?\'>([^<]+)</a>|is', $classes , $arr_class);
		//print_r($arr_class);exit;
		foreach ($arr_class[3] as $k => $val){
		
			$data	= array(
					'class_1'	=> $arr_class[1][$k],
					'class_2'	=> $arr_class[2][$k],
					'class_3'	=> $val,
					'class_name'	=> $arr_class[4][$k],
			);
			$this->dbs->insert_db($data , 'hzp_class_3');
		}
		//print_r($arr_class);
	}
	
	/**
	 * 获取礼品箱包分类
	 */
	public function xb(){
		
		header("Content-type: text/html; charset=utf-8");
		$url		= 'http://channel.jd.com/bag.html';
		//$this->curl->proxy('173.213.96.229', 8089);
		$classes	= $this->curl->simple_get($url);
		preg_match_all('|<a  title=".*?" href="http://list.jd.com/([0-9]+)-([0-9]+)-([0-9]+).html">([^<]+)</a>|is', $classes , $arr_class);
		//preg_match_all('|<a href="http://list.jd.com/([0-9]+)-([0-9]+)-([0-9]+).html" title=\'.*?\'>([^<]+)</a>|is', $classes , $arr_class);
		//print_r($arr_class);exit;
		foreach ($arr_class[3] as $k => $val){
		
			$data	= array(
					'class_1'	=> $arr_class[1][$k],
					'class_2'	=> $arr_class[2][$k],
					'class_3'	=> $val,
					'class_name'	=> $arr_class[4][$k],
			);
			$this->dbs->insert_db($data , 'xb_class_3');
		}
	}
}