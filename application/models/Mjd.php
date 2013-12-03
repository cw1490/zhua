<?php
/**
 * 家电入库
 */
class Mjd extends CI_Model{

	private $dbs;
	public function __construct(){

		parent::__construct();
		$this->dbs	= $this->load->database('default',true);
		$this->load->helper('goods');
	}
	
	public function deal_class_brands(){
		$this->dbs->select('param4');
		$this->dbs->distinct();
		$this->dbs->from('up_myi_goods');
		//$this->dbs->where('');
		//$this->dbs->limit(10);
		$classes	= $this->dbs->get()->result_array();
		foreach ($classes as $k => $val){
			$class_id	= substr($val['param4'], 0 ,9);
			$brand_id	= substr($val['param4'], 9);
			if ($brand_id != 0) {
				$list[$class_id][]	= $brand_id;
				//echo $class_id . ' => array(),';
			}
			
		}
		
		foreach ($list as $cc => $vv){
			$str	= '';
			for ($i = 0; $i < count($vv); $i++){
				$str	.= '$brands['.$vv[$i].'],';
				
				//
			}
			echo $cc . ' => array('.$str.'),' . "<br>";
			//echo $str;exit;
// 			foreach ($vv as $ck => $cv){
// 				echo $cc . ' => array($brands['.$cv.']),'."<br>";
// 			}
		}
		//echo $this->dbs->last_query();
		//print_r($list);
	}
	/**
	 * 生成最终入库class
	 */
	public function deal_class(){
		
		$this->dbs->select('*');
		$this->dbs->from('jd_myi_class');
		//$this->dbs->where('aid < ',35);
		
		$classes	= $this->dbs->get()->result_array();
		foreach ($classes as $k => $val){
			$list[$val['id']]	= $val;
			echo "array('id' => " . $val['c1'] . $val['c2'] . $val['c3'] . " , 'class_id' => '" . $val['c3'] . "' , 'class_name' => '" . $val['class_name'] . "', 'parent_id' => '" . $val['c1'] . $val['c2'] . "' ),<br>";
		}
		//print_r($classes); 
	}
	/**
	 * 处理家电数据并入库
	 */
	public function jd(){

		$i	= 0;
		while($i >=0){
			$data	= array();
			//$this->dbs->distinct('jd_id');
			$this->dbs->select('*')->from('computer');
			//$this->dbs->where('class_3' , 880);
			$this->dbs->where('id > ' , 56391);
			$this->dbs->limit(1,$i);
			$res	= $this->dbs->get()->result_array();
			//echo $this->dbs->last_query();exit;
			//print_r($res);
			if(!empty($res)){//919459
		
				//print_r($res);exit;
				$i = $i + 1;
				$data 	= array(
						//'goods_name'	=> $res[0]['name'],
						'goods_name'	=> $res[0]['title'] . ' ' . $res[0]['title_1'],
						'goods_pic'		=> $res[0]['thumb'],
						'g_id'			=> $res[0]['jd_id'],
				);
		
		
				$classes	= $this->_get_class_info($res[0]['class_3']);
				$data		= array_merge($data , $classes);//分类信息
		
				$brands		= $this->_get_brand_info($res[0]['brand_name']);
				$data['brand_id'] = false == $brands ? 0 : $brands;
		
		
		
				$goods_id	= createGoodsId('',$classes['class_id'],'e');
				$data['goods_id']	= $goods_id;
				//print_r($data);exit;
					
		
				$this->dbs->insert('up_digi_goods' , $data);
				$param_data	= array(
						'goods_id'		=> $goods_id,
						'goods_param'	=> $res[0]['params'],
						'goods_pics'	=> serialize($res[0]['thumb']),
				);
				$this->dbs->insert('up_digi_goods_param' , $param_data);
				echo $res[0]['id'] . "\n";
			}else{
				$i = -1;
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
}