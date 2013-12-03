<?php
/**
 * 处理淼哥数据
 */
class Mg extends CI_Model{
	
	public function __construct(){
		
		parent::__construct();
		$this->dbs	= $this->load->database('default',true);
		$this->load->helper('goods');
	}
	
	/**
	 * 将淼哥品牌处理并入库
	 */
	public function deal_brand_from_miao(){
		
		$i	= 0;
		while($i >=0){
			$this->dbs->select('*')->from('jd_brand_copy');
			$this->dbs->where('id > ',24923);
			$this->dbs->limit(1,$i);
			$brands	= $this->dbs->get()->result_array();
			if (!empty($brands)) {
				if (strpos($brands[0]['brand_name'], '（')) {
					preg_match_all('|(.*?)（.*?|is', trim($brands[0]['brand_name']) , $brand1);
					if (isset($brand1[1][0])){
						$brand	= $brand1[1][0];
					}
				}elseif (strpos($brands[0]['brand_name'], '(')){
					
					preg_match_all('|(.*?)\(.*?|is', trim($brands[0]['brand_name']) , $brand2);
					$brand	= $brand2[1][0];
				}else{
					$brand		= $brands[0]['brand_name'];
				}
				$data['brand_id']	= $brands[0]['brand_id'];
				$data['brand_name']	= $brand;
				print_r($data);
				echo "<br>";
				if (!empty($data) && isset($data['brand_name'])){
					//$this->dbs->insert('brands' , $data);
					$this->ins_brands($data,'brands');
				}
				$i++;
			}
		}
		
	}
	
	
	/**
	 * 抓分类并查询是否存在该品牌，然后入库
	 * @param unknown $param
	 * @param string $table
	 */
	public function ins_brands($param  , $table = ''){
	
			
		$exist	= $this->dbs->select('*')->from($table)->where('brand_name' , $param['brand_name'])->get()->result_array();
			
		if (empty($exist)) {
			$this->dbs->insert($table , $param);
		}
	}
	
	public function data(){
		
		$i	= 138859;
		while($i < 143662){
			
			echo $i."\n";
			$i = $i + 1;
			$data	= array();
			$this->dbs->select('*')->from('jd_good_baby');
			$this->dbs->where('id' , $i);
			//$this->dbs->limit(1,$i);
			$res	= $this->dbs->get()->result_array();
			//print_r($res);exit;
			if(!empty($res)){
				
				$this->dbs->select('*')->from('jd_good_baby_param');
				$this->dbs->where('goods_id' , $res[0]['goods_id']);
				//$this->dbs->limit(1,$i);
				$param	= $this->dbs->get()->result_array();
				/* print_r($res);
				echo "<br>--------------------------------<br>";
				print_r($param);
				echo "<br>--------------------------------<br>"; */
				if (!empty($param)) {
					
					$data	= array(
							
							'goods_name'	=> $res[0]['goods_name'],
							'goods_pic'		=> $res[0]['goods_img'],
							'goods_price'	=> $res[0]['goods_price'],
							'g_id'			=> $res[0]['goods_id'],
					);
					
					$brand_name	= $this->get_brand_name($res[0]['brand_id']);
					if (false !== $brand_name) {
						$brands	= $this->get_brand_id_by_brand_name($brand_name);
						if (false !== $brands) {
							$data['brand_id']	= $brands;
						}else{
							$data['brand_id']	= 0;
						}
					}
					//print_r($res[0]);
					//$data['brand_id']	= $this->_get_brand($res[0]['brand_id']);//品牌id
					$classes	= $this->_get_class_info($res[0]['class_2_id']);//分类
					if (!empty($classes) && is_array($classes)) {
						$data				= array_merge($data , $classes);
						$data['goods_id']	= createGoodsId('',$classes['class_id'],'m');
						//$data['pingmu']		= $this->_get_ping_mu($param[0]['param_param']);
							
						//print_r($data);exit;
							
						//$data['param1']		= $this->_get_os($param[0]['goods_param']);
							
						$info		= array(
									
								'goods_id'		=> $data['goods_id'],
								'goods_intro'	=> serialize($param[0]['goods_param']),
								'goods_param'	=> serialize($param[0]['param_param']),
								'goods_pics'	=> $param[0]['param_pics'],
						);
						/* print_r($data);
						echo "<br>";
						print_r($info);
						exit; */
							
						$this->dbs->insert('up_myi_goods' , $data);
						$this->dbs->insert('up_myi_goods_param' , $info);
					}
					
					
				}
			}
			unset($res);
			unset($param);
		}
	}
	public function get_brand_id_by_brand_name($brand_name = ''){
		
		$this->dbs->select('*')->from('brands');
		$this->dbs->like('brand_name' , $brand_name);
		$res	= $this->dbs->get()->result_array();
		if (!empty($res)) {
			return $res[0]['id'];
		}else{
			return false;
		}
	}
	public function get_brand_name($brand_id = 0){
		
		$this->dbs->select('*')->from('jd_brand_copy');
		$this->dbs->where('brand_id' , $brand_id);
		$res	= $this->dbs->get()->result_array();
		if (!empty($res) && isset($res[0]['brand_name'])) {
			return $res[0]['brand_name'];
		}else{
			return false;
		}
	}
	//获取操作系统
	private function _get_os($contents){
		//$contents	= unserialize($params);
		if (!empty($contents)) {
			preg_match_all('|<li.*?>系统：(.*?)</li>|is', $contents , $os);
			if (!isset($os[1][0])){
				
				return 8;
			}
			if (false !== strpos($os[1][0], 'WindowsPhone')) {
				return '3';
			}elseif (false !== strpos($os[1][0], 'ios') || false !== strpos($os[1][0] , 'iOS') || false !== strpos($os[1][0] , 'IOS')){
				return '2';
			}elseif(false !== strpos($os[1][0], 'Android')){
				return 1;
			}elseif (false !== strpos($os[1][0], 'WindowsMobile')){
				return '4';
			}elseif (false !== strpos($os[1][0], 'Symbian')){
				return '5';
			}elseif (false !== strpos($os[1][0], '其它智能系统')){
				return '6';
			}elseif (false !== strpos($os[1][0], '非智能操作系统 ')){
				return '7';
			}else{
				return '8';
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
	 * 获取屏幕大小
	 * @param unknown $params
	 * @return number
	 */
	private function _get_ping_mu($contents){
		//print_r($params);exit;
		//$contents$contents	= unserialize($params);
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
	 * 通过京东品牌id获取品牌信息
	 * @param number $jd_brand_id
	 */
	public function _get_brand($jd_brand_id = 0){
		
		$this->dbs->select('*')->from('brands');
		$res	= $this->dbs->where('brand_id' , $jd_brand_id)->get()->result_array();
		if (!empty($res)) {
			return $this->_get_brand_by_name($res[0]['brand_name']);
		}else{
			return 0;
		}
	}
	public function _get_brand_by_name($brand_name = ''){
		
		$this->dbs->select('*')->from('brands');
		$res	= $this->dbs->where('brand_name' , trim($brand_name))->get()->result_array();
		if (!empty($res)) {
			return $res[0]['id'];
		}else{
			return 0;
		}
	}
	
	/**
	 * 获取经过处理过的分类信息
	 * @param number $class_3
	 * @return multitype:NULL |boolean
	 */
	private function _get_class_info($class_3 = 0){
	
		$data	= array();
	
		$this->dbs->select('*')->from('jd_myi_class');
		$this->dbs->where(array('class_3' => $class_3 ));
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
	
	public function myi_class(){
		
		$this->dbs->select('*')->from('jd_class_2');
		$this->dbs->where('pid_pid' , 6233);
		$res	= $this->dbs->get()->result_array();
		foreach ($res as $k => $val){
			
			$info	= array(
					
					'class_name'	=> $val['name'],
					
					/* 'class_1'	=> 6233,
					'class_2'	=> $val['pid'],
					'class_3'	=> $val['id'],
					'c1'		=> 600,
					'c2'		=> 101,
					'c3'		=> 144 + $k, */
			);
			$this->dbs->where('class_3' , $val['id']);
			$this->dbs->update('jd_myi_class' , $info);
			//$this->dbs->insert('jd_myi_class' , $info);
			unset($info);
		}
		
		//print_r($res);
	}
}
