<?php
class Mup extends CI_Model{
	
	private $dbs;
	public function __construct(){
		
		parent::__construct();
		$this->dbs	= $this->load->database('default',true);
	}
	
	public function get_mult_params(){
		
		$res	= $this->get_goods_param_by_class_id(900, 101, 101);
		return $res;
	}
	
	public function get_goods_param_by_class_id($c1 , $c2 , $c3){
		
		$i	= 1;
		while ($i > 0){
			
			$res	= $this->dbs->select('goods_id')
							->from('up_digi_goods')
							->where(array('grand_id' => $c1 , 'parent_id' => $c2 , 'class_id' => $c3))
							->limit(1,$i)
							->get()->result_array();
			$i++;
			if (!empty($res) && isset($res[0]['goods_id'])) {
				$this->get_goods_params($res[0]['goods_id']);
			}
		}
	}
	
	/**
	 * 获取商品详情属性并拆解
	 * @param string $goods_id
	 */
	public function get_goods_params($goods_id = ''){
		$res	= $this->dbs->select('*')
						->from('up_digi_goods_param')
						->where('goods_id' , $goods_id)
						->get()->result_array();
		if (!empty($res)) {
			
			$sql	= '';
			$params	= unserialize($res[0]['goods_param']);
			
			/* 
			//核心
			$hexin	= $this->hexin($params);
			$sql	= 'UPDATE up_digi_goods SET param1 = ' . $hexin .' WHERE goods_id = "' . $goods_id . '";' . "\n";
			 */
			
			/* 
			//平台
			$pingtai	= $this->pingtai($params);
			$sql	= 'UPDATE up_digi_goods SET param1 = ' . $pingtai .' WHERE goods_id = "' . $goods_id . '";' . "\n";
			 */
			
			/* 
			$yingpan	= $this->yingpan($params);
			$sql	= 'UPDATE up_digi_goods SET param2 = ' . $yingpan .' WHERE goods_id = "' . $goods_id . '";' . "\n";
			 */
			
			/* 
			$os	= $this->os($params);
			$sql	= 'UPDATE up_digi_goods SET param1 = ' . $os .' WHERE goods_id = "' . $goods_id . '";' . "\n";
			 */
			
			/* 
			$cpu	= $this->cpu($params);
			$sql	= 'UPDATE up_digi_goods SET param1 = ' . $cpu .' WHERE goods_id = "' . $goods_id . '";' . "\n";
			 */
			
			
			//屏幕
			$pingmu	= $this->pingmu($params);
			$sql	= 'UPDATE up_digi_goods SET pingmu = ' . $pingmu .' WHERE goods_id = "' . $goods_id . '";' . "\n"; 
			
			
			file_put_contents('2.sql', $sql , FILE_APPEND);
		}
		print_r($sql);
	}
	//获取平台
	private function pingtai($contents){
		if (!empty($contents)) {
			preg_match_all('|<td.*?>平台.*?</td><td>(.*?)</td>|is', $contents , $pingtai);
			if(false !== strpos($pingtai[1][0] , 'VIA') || strpos($pingtai[1][0] , 'via')){
				return 3;
			}elseif(false !== strpos($pingtai[1][0] , 'Intel')){
				return 1;
			}elseif (false !== strpos($pingtai[1][0] , "AMD")) {
				return 2;
			}
		}
	}
	
	//获取cpu的核心数
	private function hexin($contents){
		if (!empty($contents)) {
			preg_match_all('|<td.*?>核心数量</td><td>(.*?)</td>|is', $contents , $hexin);
			if(false !== strpos($hexin[1][0] , '双')){
				return 1;
			}elseif(false !== strpos($hexin[1][0] , '四')){
				return 2;
			}elseif (false !== strpos($hexin[1][0] , "六")) {
				return 3;
			}elseif (false !== strpos($hexin[1][0] , "八")) {
				return 4;
			}
		}
	}
	//获取cpu型号
	public function cpu($params = ''){
		$data = 7;
		if (!empty($params)) {
			preg_match_all('|<td>CPU型号</td><td>([^-]+).*?</td>|is', $params , $cpu);
			if (isset($cpu[1][0])) {
				switch (trim($cpu[1][0])){
					case 'i3': $data = 1; break;
					case 'i5': $data = 2; break;
					case 'i7': $data = 3; break;
					case 'A6': $data = 4; break;
					case 'A8': $data = 5; break;
					case 'A10': $data = 6; break;
				}
			}
		}
		return $data;
	}
	/**
	 * 获取屏幕信息
	 */
	public function pingmu($params = ''){
		$data	= 0;
		if (!empty($params)) {
			preg_match_all('|<td.*?>屏幕规格</td><td>(.*?)英寸.*?</td>|is', $params , $pm);
			if(isset($pm[1]) && !empty($pm[1])){
				return $pm[1][0];
			}else{
				preg_match_all('|<td.*?>屏幕尺寸</td><td>(.*?)英寸.*?</td>|is', $params , $pm2);
				if(isset($pm2[1]) && !empty($pm2[1])){
					return $pm2[1][0];
				}else{
					preg_match_all('|<td.*?>尺寸</td><td>(.*?)英寸.*?</td>|is', $params , $pm3);
					if(isset($pm3[1]) && !empty($pm3[1])){
						return $pm3[1][0];
					}else{
						return 0;
					}
				}
			}
		}
	}
	
	//获取操作系统
	private function os($contents){
		if (!empty($contents)) {
			preg_match_all('|<td.*?>操作系统</td><td>(.*?)</td>|is', $contents , $os);
			if (false !== strpos($os[1][0], 'windows')) {
				return '3';
			}elseif (false !== strpos($os[1][0], 'ios') || strpos($os[1][0] , 'iOS') || strpos($os[1][0] , 'IOS')){
				return '2';
			}elseif(false !== strpos($os[1][0], 'Android')){
				return 1;
			}else{
				return '4';
			}
		}
	}
	
	//获取硬盘大小
	private function yingpan($contents){
		if (!empty($contents)) {
			preg_match_all('|<td.*?>.*?容量</td><td>(.*?)G.*?</td>|is', $contents , $pm);
			if(isset($pm[1]) && !empty($pm[1])){
				return $pm[1][0];
			}else{
				return 0;
			}
		}
	}
	
}