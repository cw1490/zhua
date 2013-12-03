<?php
/**
 * 商品操作相关类
 * author：ryan
 * time：2012-11-8
 */
/**
 * 生成商品id
 * @param  $user_id 发布者id，第一版写死 发布者为管理员
 * @param  $class_id 分类id
 * 生成规则：1-4 随机数 5-7 表名 8-11 用户id 12-15 随机数 16-18 classid 19-26 时间戳
 * table 中关村数据 elc 红孩子 myi 汽车 car
 */
function createGoodsId( $user_id = '' , $class_id = 999 , $table = 'e'){
	if (empty($user_id)){
		$user_id	= mt_rand(900000 , 999999);
	}
	if(empty($class_id)) {
		return null;
	}else{
		$rand      	= mt_rand(100000, 999999);//4位随机数 1-6位
		$table		= trim($table);	//1位 7
		$uid 		= base_convert($user_id,10,36);//将用户id转为36进制
		$uid		= str_pad($uid,4,'0',STR_PAD_LEFT);//发布者id部分补全为4位 8-11
		$rang2		= mt_rand(10000,99999);
		$lastpix	= base_convert($rang2,10,36);
		$lastpix   	= str_pad($lastpix, 4, '0', STR_PAD_LEFT); //12-15位
		$class_id	= str_pad($class_id , 3 , '0' ,STR_PAD_LEFT);//16-18
		$time 	   	= base_convert( $_SERVER['REQUEST_TIME'], 10, 36 );
		$time 	  	= str_pad( $time, 8, '0', STR_PAD_LEFT );//19-26
		return $rand . $table . $uid . $lastpix . $class_id . $time;
	}
}


/**
 * 商铺评论平均分计算
 */
if(!function_exists('avg_shop_cmt')) {
	function avg_shop_cmt($arr) {
		return avg_goods_cmt($arr);
	}
}

/**
 * 商铺评论总分计算
 */
if(!function_exists('sum_shop_cmt')) {
	function sum_shop_cmt($arr) {
		return sum_goods_cmt($arr);
	}
}

/**
 * 商品评论平均分计算
 * dth
 * 20111116
 *
 * @arr	= array(
 * 	'cmt_5'	=> int,
 * 	'cmt_4'	=> int,
 * 	'cmt_3'	=> int,
 * 	'cmt_2'	=> int,
 * 	'cmt_1'	=> int,
 * )
 *
 * @return int
 *
 */
if(!function_exists('avg_goods_cmt')) {
	function avg_goods_cmt($arr)
	{
		$result	= 0;
		if(!is_array($arr) || empty($arr)) {
			return $result;
		}

		$cmt5		= isset($arr['cmt_5']) ? $arr['cmt_5'] : 0;
		$cmt4		= isset($arr['cmt_4']) ? $arr['cmt_4'] : 0;
		$cmt3		= isset($arr['cmt_3']) ? $arr['cmt_3'] : 0;
		$cmt2		= isset($arr['cmt_2']) ? $arr['cmt_2'] : 0;
		$cmt1		= isset($arr['cmt_1']) ? $arr['cmt_1'] : 0;

		$result	= 5 * $cmt5 +
		3 * $cmt4 +
		0 * $cmt3 +
		(-3) * $cmt2 +
		(-5) * $cmt1;
		return $result == 0 ? 0 : number_format(floatval($result / ($cmt1 + $cmt2 + $cmt3 + $cmt4 + $cmt5)),1,'.','');
	}
}

/**
 * 商品评论总分计算
 * dth
 * 20111116
 *
 * @arr	= array(
 * 	'cmt_5'	=> int,
 * 	'cmt_4'	=> int,
 * 	'cmt_3'	=> int,
 * 	'cmt_2'	=> int,
 * 	'cmt_1'	=> int,
 * )
 *
 * @return int
 *
 */
if(!function_exists('sum_goods_cmt')) {
	function sum_goods_cmt($arr)
	{
		$result	= 0;
		if(!is_array($arr) || empty($arr)) {
			return $result;
		}

		$cmt5		= isset($arr['cmt_5']) ? $arr['cmt_5'] : 0;
		$cmt4		= isset($arr['cmt_4']) ? $arr['cmt_4'] : 0;
		$cmt3		= isset($arr['cmt_3']) ? $arr['cmt_3'] : 0;
		$cmt2		= isset($arr['cmt_2']) ? $arr['cmt_2'] : 0;
		$cmt1		= isset($arr['cmt_1']) ? $arr['cmt_1'] : 0;

		$result	= 5 * $cmt5 +
		3 * $cmt4 +
		0 * $cmt3 +
		(-3) * $cmt2 +
		(-5) * $cmt1;
		return $result;
	}
}

/**
 * 生成商品列表页面url
 * $brand_id = '0' , $class_id = '16' , $price_between = '' , $print_size = '' , $order = '' , $mode = '0' , $type = 1
 */
if(!function_exists('goodslist_url')) {
	function goodslist_url($args,$copy=array())
	{
		$args['brand_id'] 		= isset($args['brand_id']) 		&& !empty($args['brand_id']) 		? $args['brand_id'] 		: 0;
		$args['class_id'] 		= isset($args['class_id']) 		&& !empty($args['class_id'])		? $args['class_id'] 		: 0;
		$args['price_between'] 	= isset($args['price_between']) 		&& !empty($args['price_between'])	? $args['price_between'] 	: 0;
		$args['print_size'] 	= isset($args['print_size']) 			&& !empty($args['print_size'])		? $args['print_size'] 		: 0;
		$args['order'] 			= isset($args['order']) 		&& !empty($args['order'])		? $args['order'] 		: 0;
		$args['mode'] 			= isset($args['mode']) 			&& !empty($args['mode'])		? $args['mode'] 		: 0;
		$args['type'] 			= isset($args['type']) 	&& !empty($args['type']) ? $args['type'] 	: 0;

		if(count($copy) > 0){
			foreach($copy as $key => $v){
				$args[$key] = $v;
			}
		}
		return site_url('product/goodslist/index/'.$args['brand_id'].'/'.$args['class_id'].'/'.$args['price_between'].'/'.$args['print_size'].'/'.$args['order'].'/'.$args['mode'].'/'.$args['type']);
	}
}


/**
 * 生成商品列表页面url
 * $brand_id = '0' , $class_id = '16' , $price_between = '' , $print_size = '' , $order = '' , $mode = '0' , $type = 1
 */
if(!function_exists('library_url')) {
	function library_url($args,$copy=array(),$type='index')
	{
		$args['table'] 			= isset($args['table']) 		&& !empty($args['table']) 			? $args['table'] 			: 0;
		$args['parent_id'] 		= isset($args['parent_id']) 	&& !empty($args['parent_id'])		? $args['parent_id'] 		: 0;
		$args['class_id'] 		= isset($args['class_id']) 		&& !empty($args['class_id'])		? $args['class_id'] 		: 0;
		$args['brand_id'] 		= isset($args['brand_id']) 		&& !empty($args['brand_id'])		? $args['brand_id'] 		: 0;
		$args['order'] 			= isset($args['order']) 		&& !empty($args['order'])			? $args['order'] 			: 0;
		$args['price_between'] 	= isset($args['price_between']) && !empty($args['price_between'])	? $args['price_between'] 	: 0;
		$args['print_size'] 	= isset($args['print_size']) 	&& !empty($args['print_size']) 		? $args['print_size'] 		: 0;
		$args['param1'] 		= isset($args['param1']) 		&& !empty($args['param1']) 			? $args['param1'] 			: 0;//对应数据库中param1值
		if(count($copy) > 0){
			foreach($copy as $key => $v){
				$args[$key] = $v;
			}
		}
		return site_url('product/library/'.$type.'/'.$args['table'].'/'.$args['parent_id'].'/'.$args['class_id'].'/'.$args['brand_id'].'/'.$args['order'].'/'.$args['price_between'].'/'.$args['print_size'].'/'.$args['param1']);
	}
}

/**
 * 商品流行页面url
 * $order = 0,$class_id =0,$table=0,
 */
if(!function_exists('fashion_url')) {
	function fashion_url($args,$copy = array()){
		$args['order']		 = isset($args['order']) && !empty($args['order']) ? $args['order']: 0;
		$args['class_id'] 	 = isset($args['class_id']) && !empty($args['class_id']) ? $args['class_id']: 0;
		$args['table']		 = isset($args['table']) && !empty($args['table']) ? $args['table']: 0;
		if(count($copy) > 0){
			foreach($copy as $key=>$v){
				$args[$key]=$v;
			}
		}
		return site_url('shows/slist/fashion/'.$args['order'].'/'.$args['class_id'].'/'.$args['table']);
	}
}

/**
 * 商品晒货页面url
 * $order = 0,$class_id =0,$table=0,
 */
if(!function_exists('shaihuo_url')) {
	function shaihuo_url($args,$copy = array()){
		$args['order']		 = isset($args['order']) && !empty($args['order']) ? $args['order']: 0;
		$args['class_id'] 	 = isset($args['class_id']) && !empty($args['class_id']) ? $args['class_id']: 0;
		$args['table']		 = isset($args['table']) && !empty($args['table']) ? $args['table']: 0;
		if(count($copy) > 0){
			foreach($copy as $key=>$v){
				$args[$key]=$v;
			}
		}
		return site_url('shows/slist/shaihuo/'.$args['order'].'/'.$args['class_id'].'/'.$args['table']);
	}
}

/**
 *生成商品简介（搜索页商品简介过长，取前四个） 
 *@param goods_intro:简介。$k 个数（从0开始）
 *@return str
 *@author W_uniQue 2013/03/04
 */
function get_intro($goods_intro,$k = 4){
	$intro= htmlspecialchars(unserialize($goods_intro));	//反序列化，html标签实体化
	$arr= explode('/li&gt;',$intro);
	$total = count($arr);
	if($total >=$k){
			$str='';
			for($i=0;$i<$k;$i++){
					$str.=$arr[$i].'/li&gt;';
			}
			echo htmlspecialchars_decode($str);
		}else{
			echo htmlspecialchars_decode($intro);
		}
	
}

/**
 * 通过商品自增id和商品di获取到拼接后的商品详情页id
 * @param  $auto_id
 * @param  $goods_id
 */
if (!function_exists('get_goods_muti_id')) {

	function get_goods_auto_id($auto_id = '' , $goods_id = ''){
		$type	= get_goods_type($goods_id);
		return $type . $auto_id;
	}
}
/**
 * 通过拼接后的商品id获取商品详情url
 * @param string $muti_id
 */
if (!function_exists('get_goods_url_by_muti_id')) {
	
	function get_goods_url_by_muti_id($muti_id = ''){
		if (isset($muti_id)) {
			return site_url('product/'.$muti_id.'.html');
		}else{
			return site_url();
		}
	}
}
/**
 * 生成商品详情页面URL  $goods = array()      如：array('goods_id' => 'fsdafsdafasdfasf')
 **/
function goods_url($goods_id)
{
	if(isset($goods_id)){
		return site_url('product/'.$goods_id.'.html');
	}else{
		return site_url();
	}
}

if(!function_exists('get_goods_type')) {
	function get_goods_type($goods_id){
		$type	= substr($goods_id, 4 , 3);
		switch ($type){
			case 'elc':
				$res	= 'e';
				break;
			case 'myi':
				$res	= 'm';
				break;
			case 'hzp':
				$res	= 'h';
				break;
		}
		return $res;
	}
}
