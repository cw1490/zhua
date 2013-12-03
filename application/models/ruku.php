<?php
/**
 * 入库
 */
class Ruku extends CI_Model{

	private $dbs;
	public function __construct(){

		parent::__construct();
		$this->dbs	= $this->load->database('default',true);
		$this->load->helper('goods');
	}
	
	
	//$sql	= 'select distinct class_id,brand_id  from uping.up_digi_goods';
	
	public function creat_cat_brand(){
	
	}
	/**
	 * 生存品牌配置文件，直接打印出，然后复制
	 */
	public function creat_brand(){
		
		$res	= $this->dbs->select('id,brand_name')->from('brands')->get()->result_array();
		
		foreach ($res as $k => $val){
			$list[$val['id']]	= $val;
			echo $val['id'] . " => array('id' => " . $val['id'] . " , 'brand_name' => \"" . $val['brand_name'] . "\"),<br>";  
		}
		//print_r($list);
	}
	public function pc(){
		
		$i	= 0;
		while($i >=0){
			$data	= array();
			//$this->dbs->distinct('jd_id');
			$this->dbs->select('*')->from('computer');
			$this->dbs->where('class_3' , 681);
			//$this->dbs->where('jd_id' , 908351);
			$this->dbs->limit(10,$i);
			$res	= $this->dbs->get()->result_array();
			//echo $this->dbs->last_query();exit;
			if(!empty($res)){//919459
				
				//print_r($res);exit;
				$i = $i + 1;
				$data 	= array(
						//'goods_name'	=> $res[0]['name'],
						'goods_name'	=> $res[0]['title'] . ' ' . $res[0]['title_1'],
						'goods_pic'		=> $res[0]['thumb'],
						'g_id'			=> $res[0]['jd_id'],
				);

				/*
				$ying_pan	= $this->_get_ying_pan($res[0]['params']);
				$data['param2']	= $ying_pan;	//将内存硬盘容量放入到param2中
				
				$os			= $this->_get_os($res[0]['params']);
				$data['param1']	= $os;
				
				$cpu	= $this->_get_cpu_type($res[0]['params']);//获取cpu
				$data['param1']	= $cpu;
				
				
				

				$data['pingmu']	= $this->_get_ping_mu($res[0]['params']);

				$cpu_he_xin = $this->_get_cpu_he_xin($res[0]['params']);//获取cpu核心数
				$data['param1'] = $cpu_he_xin;

				$ping_tai 	= $this->_get_ping_tai($res[0]['params']);//获取平台
				///print_r($ping_tai);
				//$data['param1']	= $ping_tai;
				*/
				$ping_tai 	= $this->_get_ping_tai($res[0]['params']);//获取平台
				$data['param1']	= $ping_tai;


				$classes	= $this->_get_class_info($res[0]['class_3']);
				$data		= array_merge($data , $classes);//分类信息
				
				$brands		= $this->_get_brand_info($res[0]['brand_name']);
				$data['brand_id'] = $brands;
				
				
				
				$goods_id	= createGoodsId('',$classes['class_id'],'e');
				$data['goods_id']	= $goods_id;
 				print_r($data);exit;
 				

				$this->dbs->insert('up_digi_goods' , $data);
				$param_data	= array(
						'goods_id'		=> $goods_id,
						'goods_param'	=> $res[0]['params'],
						'goods_pics'	=> serialize($res[0]['thumb']),
				);
				$this->dbs->insert('up_digi_goods_param' , $param_data);
			}else{
				$i = -1;
			}
		}
	}
	//平台类型
	//获取cpu的核心数
	private function _get_cpu_he_xin($params){
		$contents	= unserialize($params);
		//print_r($contents);
		if (!empty($contents)) {
			preg_match_all('|<td.*?>核心数量</td><td>(.*?)</td>|is', $contents , $hexin);

			if(false !== strpos($hexin[1][0] , '双')){
				return 1;
			}elseif(false !== strpos($hexin[1][0] , '四')){
				//echo 'cc';
				return 2;
			}elseif (false !== strpos($hexin[1][0] , "六")) {
				//echo 'dd';
				return 3;
			}elseif (false !== strpos($hexin[1][0] , "八")) {
				//echo 'dd';
				return 4;
			}
		}
	}

	/**
	*	获取平台信息 如intel ，amd
	*/
	private function _get_ping_tai($params){
		//header("Content-type: text/html; charset=utf-8");
		$contents	= unserialize($params);
		//print_r($contents);
		if (!empty($contents)) {
			preg_match_all('|<td.*?>平台.*?</td><td>(.*?)</td>|is', $contents , $pingtai);
			//print_r($pingtai[1][0]);
			if(false !== strpos($pingtai[1][0] , 'VIA') || strpos($pingtai[1][0] , 'via')){
				return 3;
			}elseif(false !== strpos($pingtai[1][0] , 'Intel')){
				//echo 'cc';
				return 1;
			}elseif (false !== strpos($pingtai[1][0] , "AMD")) {
				//echo 'dd';
				return 2;
			}
		}

	}
	//获取操作系统
	private function _get_os($params){
		$contents	= unserialize($params);
		if (!empty($contents)) {
		
			preg_match_all('|<td.*?>操作系统</td><td>(.*?)</td>|is', $contents , $os);
			if (false !== strpos($os[1][0], 'windows')) {
				return '3';
			}elseif (false !== strpos($os[1][0], 'ios') || strpos($os[1][0] , 'iOS')){
				return '2';
			}elseif(false !== strpos($os[1][0], 'Android')){
				return 1;
			}else{
				return '4';
			}
			//print_r($os);exit;
			/* if(isset($pm[1]) && !empty($pm[1])){
				return $pm[1][0];
			}else{
				return 0;
			} */
		}
	}

	/**
	 * 获取硬盘大小
	 * @param unknown $params
	 * @return number
	 */
	private function _get_ying_pan($params){
		$contents	= unserialize($params);
		if (!empty($contents)) {
				
			preg_match_all('|<td.*?>.*?容量</td><td>(.*?)G.*?</td>|is', $contents , $pm);
			if(isset($pm[1]) && !empty($pm[1])){
				return $pm[1][0];
			}else{
				return 0;
			}
		}
	}
	/**
	 * 获取品牌信息
	 * @param string $brand_name
	 * @return boolean
	 */
	private function _get_brand_info($brand_name = ''){
		
		$this->dbs->select('*')->from('brands');
		$this->dbs->where('brand_name' , trim($brand_name));
		$res	= $this->dbs->get()->result_array();
		if (!empty($res)) {
			return $res[0]['id'];
		}else{
			return false;
		}
	}
	/**
	 * 获取经过处理过的分类信息
	 * @param number $class_3
	 * @return multitype:NULL |boolean
	 */
	private function _get_class_info($class_3 = 0){
		
		$data	= array();
		
		$this->dbs->select('*')->from('class_3');
		$this->dbs->where('class_3' , $class_3);
		$res	= $this->dbs->get()->result_array();
		if (!empty($res)) {
			$data	= array(
				
				'grand_id'	=> $res[0]['c1'],
				'parent_id'	=> $res[0]['c2'],
				'class_id'	=> $res[0]['c3'],
			);
			return $data;
		}else{
			return false;
		}
		
	}
	/**
	 * 获取屏幕大小
	 * @param unknown $params
	 * @return number
	 */
	private function _get_ping_mu($params){
		$contents	= unserialize($params);
		if (!empty($contents)) {
			
			preg_match_all('|<td.*?>屏幕规格</td><td>(.*?)英寸.*?</td>|is', $contents , $pm);
			if(isset($pm[1]) && !empty($pm[1])){
				return $pm[1][0];
			}else{
				preg_match_all('|<td.*?>屏幕尺寸</td><td>(.*?)英寸.*?</td>|is', $contents , $pm2);
				if(isset($pm2[1]) && !empty($pm2[1])){
					return $pm2[1][0];
				}else{
					preg_match_all('|<td.*?>尺寸</td><td>(.*?)英寸.*?</td>|is', $contents , $pm3);
					if(isset($pm3[1]) && !empty($pm3[1])){
						return $pm3[1][0];
					}else{
						return 0;
					}
				}
			}
		}else{
			return 0;
		}
	}
	/**
	 * 获取cpu信息 i3,i5神马的，索引用地
	 * @param unknown $params
	 * @return number
	 */
	private function _get_cpu_type($params){
		
		$contents	= unserialize($params);
		$data = 7;
		if (!empty($contents)) {
			preg_match_all('|<td>CPU型号</td><td>([^-]+).*?</td>|is', $contents , $cpus);
			
			if (isset($cpus[1][0])) {
				switch (trim($cpus[1][0])){
					case 'i3':
						$data = 1;
						break;
					case 'i5':
						$data = 2;
						break;
					case 'i7':
						$data = 3;
						break;
					case 'A6':
						$data = 4;
						break;
					case 'A8':
						$data = 5;
						break;
					case 'A10':
						$data = 6;
						break;
				}
			}
		}
		return $data;
	}
	
	
	public function deal_class(){
		$this->dbs->select('class_3');
		//$this->dbs->distinct('class_2');
		$this->dbs->from('class_3');
		$res	= $this->dbs->get()->result_array();
		foreach ($res as $k =>$val){
			$data	= array('c3' => 100 + $k);
			$this->dbs->where('class_3' , $val['class_3']);
			$this->dbs->update('class_3' , $data);
			
		}
		print_r($res);
	}



	/**
	*	笔记本数据入库
	*/
	public function deal_note(){

		$this->dbs->select('*')->from('computer');
		$this->dbs->where('id' , 1);
		$res	= $this->dbs->get()->result_array();

		print_r($res);
	}
};