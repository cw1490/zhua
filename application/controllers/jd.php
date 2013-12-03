<?php
/**
 * 获取家电 url : http://channel.jd.com/electronic.html
 */
class Jd extends CI_Controller{
	
	private $url	= null;
	public function __construct(){
		
		parent::__construct();
		$this->load->model('dbs');
		$this->url	= 'http://channel.jd.com/electronic.html';
	}
	public function index(){
		
		$this->dbs->ryan();
		/* header("Content-type:text/html;charset=gbk");
		$url		= 'http://channel.jd.com/electronic.html';
		$content	= $this->curl->simple_get($url);
		print_r($content); */
		
	}
	/**
	 * 获取
	 */
	public function get_1_class(){
		
		
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
		$url		= 'http://channel.jd.com/electronic.html';
		//$this->curl->proxy('173.213.96.229', 8089);
		$classes	= $this->curl->simple_get($url);
		//preg_match_all('|<a  title=".*?" href="http://www.jd.com/products/([^-]+)-([^-]+)-([^-]+)-0-0-0-0-0-0-0-1-1-1-1-72-33.html">([^<]+)</a>|is', $classes , $arr_class);
		preg_match_all('|<a title=".*?" href="http://www.jd.com/products/([0-9]+)-([0-9]+)-([0-9]+).html">([^<]+)</a>|is', $classes , $arr_class);
		//print_r($arr_class);exit;
		foreach ($arr_class[3] as $k => $val){
				
			$data	= array(
					'class_1'	=> $arr_class[1][$k],
					'class_2'	=> $arr_class[2][$k],
					'class_3'	=> $val,
					'class_name'	=> $arr_class[4][$k],
			);
			$this->dbs->insert_db($data , 'jd_class_3');
		}
		//print_r($arr_class);
	}
}