<?php
/**
 * db层
 */
class Dbs extends CI_Model{
	
	private $dbs;
	public function __construct(){
		
		parent::__construct();
		$this->dbs	= $this->load->database('default',true);
	}
	
	public function insert_db($param  , $table = ''){
		
		$this->dbs->insert($table , $param);
		
		echo $this->dbs->insert_id() . "\n";
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
	public function get_db($where = array(), $table , $i = 0){
		
		
		$this->dbs->select('id,jd_id');
		$this->dbs->from($table);
		$this->dbs->where('id >' , 34825);
		$this->dbs->where('jd_id <' , 999999);
		$this->dbs->limit(100,$i);
		$res	= $this->dbs->get()->result_array();
		//echo $res[0]['id'] . "\n";
		return $res;
	}
	
	public function up_data($table , $data , $jd_id){
		
		$this->dbs->where('jd_id' , $jd_id);
		$this->dbs->update($table , $data);
	}
	
	public function ryan(){
		$sql	= "select * from jd_brand_copy where brand_name regexp '（.*）'";
		$res	= $this->dbs->query($sql)->result_array();
		foreach ($res as $k => $val){
			//print_r($res[$k]['brand_name']);exit;
			//preg_replace('|（.*?）|is', '' ,$res[$k]['brand_name']);
			preg_match_all('|(.*?)\(|is', $val['brand_name'],$cc);
			//print_r(array('brand_name',$cc[1][0]));exit;
			$this->dbs->where('id',$val['id']);
			$this->dbs->update('jd_brand_copy',array('brand_name'=>$cc[1][0]));
			//echo $this->dbs->last_query();exit;
			print_r($cc[1][0]);
		}
		print_r($res);
		
	}
	public function get_brands(){
		
	}
}