<?php
/**
 * 处理有评网数据
 * 
 */
class Uping extends CI_Controller{
	
	public function __construct(){
		
		parent::__construct();
		$this->load->model('mup');
	}
	
	public function index(){
		
		$rs	= $this->mup->get_mult_params();
		print_r($rs);
	}
}