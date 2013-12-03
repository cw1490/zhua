<?php
/**
 * 处理抓来数据并入库
 * by ：ryan
 */
class Deal extends CI_Controller{
	
	public function __construct(){
		
		parent::__construct();
		$this->load->model('ruku');
		$this->load->model('mjd');
		$this->load->model('mhzp');
	}

	/**
	 * 处理淼哥数据
	 */
	public function mg_myi_class(){
		
		$this->load->model('mg');
		$res	= $this->mg->myi_class();
	}
	public function mg(){
		$this->load->model('mg');
		$res	= $this->mg->data();
	}
	
	public function mg_brand(){
		$this->load->model('mg');
		$res	= $this->mg->deal_brand_from_miao();
	}
	public function class_brand(){
		$res	= $this->mjd->deal_class_brands();
	}
	//处理分类
	public function cls(){
		$res	= $this->mjd->deal_class();
	}
	/**
	 * 家电
	 */
	public function jd(){
		$res	= $this->mjd->jd();
	}
	/**
	 * 化妆品
	 */
	public function hzp(){
		$res	= $this->mhzp->hzp();
	}
	/**
	 * 电脑办公
	 */
	public function pc(){
		header("Content-type: text/html; charset=utf-8");
		$res	= $this->ruku->pc();
	}
	//生成品牌
	public function creat_brand(){
		
		$this->ruku->creat_brand();
	}
	public function creat_cat_brand(){
		$this->ruku->creat_cat_brand();
	}
}