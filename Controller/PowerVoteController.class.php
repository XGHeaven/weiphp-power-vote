<?php

namespace Addons\PowerVote\Controller;

use Home\Controller\AddonsController;

class PowerVoteController extends AddonsController {
	protected $model;
	protected $option;
	public function __construct() {
		parent::__construct ();
		$this->model = M ( 'Model' )->getByName ( 'power_vote' );
//		$this->model = M ( 'Model' )->getByName ( 'V' );
		$this->model || $this->error ( '模型不存在！' );
		
		$this->assign ( 'model', $this->model );
		
		$this->option = M ( 'Model' )->getByName ( 'power_vote_option' );
		$this->assign ( 'option', $this->option );
	}
	/**
	 * 显示指定模型列表数据
	 */
	public function lists() {
		$page = I ( 'p', 1, 'intval' ); // 默认显示第一页数据
		                                
		// 解析列表规则
		$list_data = $this->_list_grid ( $this->model );
		$grids = $list_data ['list_grids'];
		$fields = $list_data ['fields'];
		$app = get_token_appinfo();
		
		// 关键字搜索
		$map ['token'] = get_token ();
		$key = $this->model ['search_key'] ? $this->model ['search_key'] : 'title';
		if (isset ( $_REQUEST [$key] )) {
			$map [$key] = array (
					'like',
					'%' . htmlspecialchars ( $_REQUEST [$key] ) . '%' 
			);
			unset ( $_REQUEST [$key] );
		}
		// 条件搜索
		foreach ( $_REQUEST as $name => $val ) {
			if (in_array ( $name, $fields )) {
				$map [$name] = $val;
			}
		}
		$row = empty ( $this->model ['list_row'] ) ? 20 : $this->model ['list_row'];
		
		// 读取模型数据列表
		
		empty ( $fields ) || in_array ( 'id', $fields ) || array_push ( $fields, 'id' );
		$name = parse_name ( get_table_name ( $this->model ['id'] ), true );
		$data = M ( $name )->field ( empty ( $fields ) ? true : $fields )->where ( $map )->order ( 'id DESC' )->page ( $page, $row )->select ();
		
		/* 查询记录总数 */
		$count = M ( $name )->where ( $map )->count ();
		
		// 分页
		if ($count > $row) {
			$page = new \Think\Page ( $count, $row );
			$page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
			$this->assign ( '_page', $page->show () );
		}
		
		$this->assign ( 'list_grids', $grids );
		$this->assign ( 'list_data', $data );
		$this->meta_title = $this->model ['title'] . '列表';
		$this -> assign('app', $app);
		$this->display ( T ( 'Addons://PowerVote@PowerVote/lists' ) );
	}
	public function del() {
		$ids = I ( 'id', 0 );
		if (empty ( $ids )) {
			$ids = array_unique ( ( array ) I ( 'ids', 0 ) );
		}
		if (empty ( $ids )) {
			$this->error ( '请选择要操作的数据!' );
		}
		
		$Model = M ( get_table_name ( $this->model ['id'] ) );
		$map = array (
				'id' => array (
						'in',
						$ids 
				) 
		);
		$map ['token'] = get_token ();
		if ($Model->where ( $map )->delete ()) {
			$this->success ( '删除成功' );
		} else {
			$this->error ( '删除失败！' );
		}
	}
	public function edit() {
		// 获取模型信息
		$id = I ( 'id', 0, 'intval' );
		
		if (IS_POST) {
			$_POST ['mTime'] = time ();
			
			$Model = D ( parse_name ( get_table_name ( $this->model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $this->model ['id'] );
			if ($Model->create () && $Model->save ()) {
				// 增加选项
				D ( 'Addons://PowerVote/PowerVoteOption' )->set ( I ( 'post.id' ), I ( 'post.' ) );
				
				// 保存关键词
				D ( 'Common/Keyword' )->set ( I ( 'post.keyword' ), 'PowerVote', I ( 'post.id' ) );
				
				$this->success ( '保存' . $this->model ['title'] . '成功！', U ( 'lists' ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $this->model ['id'] );
			
			// 获取数据
			$data = M ( get_table_name ( $this->model ['id'] ) )->find ( $id );
			$data || $this->error ( '数据不存在！' );
			
			$token = get_token ();
			if (isset ( $data ['token'] ) && $token != $data ['token'] && defined ( 'ADDON_PUBLIC_PATH' )) {
				$this->error ( '非法访问！' );
			}
			
			$option_list = M ( 'power_vote_option' )->where ( 'vote_id=' . $id )->order ( '`order` asc' )->select ();
			$this->assign ( 'option_list', $option_list );
			
			$this->assign ( 'fields', $fields );
			$this->assign ( 'data', $data );
			$this->meta_title = '编辑' . $this->model ['title'];
			$this->display ( T ( 'Addons://PowerVote@PowerVote/edit' ) );
		}
	}
	public function add() {
		if (IS_POST) {
			// 自动补充token
			$_POST ['token'] = get_token ();
			$Model = D ( parse_name ( get_table_name ( $this->model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $this->model ['id'] );
			if ($Model->create () && $vote_id = $Model->add ()) {
				// 增加选项
				D ( 'Addons://PowerVote/PowerVoteOption' )->set ( $vote_id, I ( 'post.' ) );
				
				// 保存关键词
				D ( 'Common/Keyword' )->set ( I ( 'keyword' ), 'PowerVote', $vote_id );
				
				$this->success ( '添加' . $this->model ['title'] . '成功！', U ( 'lists' ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			
			$vote_fields = get_model_attribute ( $this->model ['id'] );
			$this->assign ( 'fields', $vote_fields );
			// 选项表
			$option_fields = get_model_attribute ( $this->option ['id'] );
			$this->assign ( 'option_fields', $option_fields );
			
			$this->meta_title = '新增' . $this->model ['title'];
			$this->display ( $this->model ['template_add'] ? $this->model ['template_add'] : T ( 'Addons://PowerVote@PowerVote/add' ) );
		}
	}
	protected function checkAttr($Model, $model_id) {
		$fields = get_model_attribute ( $model_id, false );
		$validate = $auto = array ();
		foreach ( $fields as $key => $attr ) {
			if ($attr ['is_must']) { // 必填字段
				$validate [] = array (
						$attr ['name'],
						'require',
						$attr ['title'] . '必须!' 
				);
			}
			// 自动验证规则
			if (! empty ( $attr ['validate_rule'] ) || $attr ['validate_type'] == 'unique') {
				$validate [] = array (
						$attr ['name'],
						$attr ['validate_rule'],
						$attr ['error_info'] ? $attr ['error_info'] : $attr ['title'] . '验证错误',
						0,
						$attr ['validate_type'],
						$attr ['validate_time'] 
				);
			}
			// 自动完成规则
			if (! empty ( $attr ['auto_rule'] )) {
				$auto [] = array (
						$attr ['name'],
						$attr ['auto_rule'],
						$attr ['auto_time'],
						$attr ['auto_type'] 
				);
			} elseif ('checkbox' == $attr ['type']) { // 多选型
				$auto [] = array (
						$attr ['name'],
						'arr2str',
						3,
						'function' 
				);
			} elseif ('datetime' == $attr ['type']) { // 日期型
				$auto [] = array (
						$attr ['name'],
						'strtotime',
						3,
						'function' 
				);
			}
		}
		return $Model->validate ( $validate )->auto ( $auto );
	}
	
	/**
	 * **************************微信上的操作功能************************************
	 */
	function show() {
		$vote_id = I ( 'id', 0, 'intval' );
		$openid = get_openid ();
		$token = get_token ();

		if (!empty(openid) && !empty(I('openid'))) {
			redirect(U('show', Array('id' => $vote_id, 'token' => $token)));
		}

		$this -> inject_can_join($vote_id);

		if ($this -> beforeJoined) {
			redirect(U('result', Array('id' => $vote_id)));
		}

		$this -> assign('app', get_token_appinfo($token));
		
		$info = $this->_getVoteInfo ( $vote_id );

//		$canJoin = ! empty ( $openid ) && ! empty ( $token ) && ! ($this->_is_overtime ( $vote_id )) && ! ($this->_is_join ( $vote_id, $this->mid, $token ));
//		$this->assign ( 'canJoin', $canJoin );
		// dump ( $canJoin );
		// dump(! empty ( $openid ));dump(! empty ( $token ));dump(! ($this->_is_overtime ( $vote_id )));dump(! ($this->_is_join ( $vote_id, $openid, $token )));
		
		$test_id = intval ( $_REQUEST ['test_id'] );
//		$this->assign ( 'event_url', event_url ( '投票', $vote_id ) );
		$this->assign('mid', $this->mid);

		$this->display ( T ( 'Addons://PowerVote@PowerVote/newshow' ) );
	}

	function detail() {
		$vote_id = I('id', 0, 'intval');
		$option_id = I('option', 0, 'intval');

		$info = $this -> _getVoteInfo($vote_id);
		$option = $this -> _getOptionInfo($vote_id, $option_id);

		if (empty($option_id)) {
			$this -> error('错误的投票候选信息');
		}

		$this -> inject_can_join($vote_id);
		$this -> assign('info', $info);
		$this -> assign('option', $option);

		$this -> display(T('Addons://PowerVote@PowerVote/detail'));
	}

	function result() {
		$vote_id = I ( 'id', 0, 'intval' );

		$this -> assign('app', get_token_appinfo(get_token()));

		$info = $this->_getVoteInfo ( $vote_id );
		$this -> inject_can_join($vote_id);

		$test_id = intval ( $_REQUEST ['test_id'] );
		$this->assign('mid', $this->mid);

		$this->display ( T ( 'Addons://PowerVote@PowerVote/result' ) );
	}
	function _getVoteInfo($id) {
		// 检查ID是否合法
		if (empty ( $id ) || 0 == $id) {
			$this->error ( "错误的投票ID" );
		}
		
		$map ['id'] = $map2 ['vote_id'] = intval ( $id );
		$info = M ( 'power_vote' )->where ( $map )->find ();
		$this->assign ( 'info', $info );
		
		$opts = M ( 'power_vote_option' )->where ( $map2 )->order ( '`order` asc' )->select ();
		foreach ( $opts as $p ) {
			$total += $p ['opt_count'];
		}
		foreach ( $opts as &$vo ) {
			$vo ['percent'] = round ( $vo ['opt_count'] * 100 / $total, 1 );
		}
		$this->assign ( 'opts', $opts );
		$this->assign ( 'num_total', $total );
		$this->assign ( 'vote_id', $id);
		return $info;
	}

	private function _getOptionInfo($vote_id, $option_id) {
		return M('power_vote_option') -> where(Array('id' => $option_id, 'vote_id' => $vote_id)) -> find();
	}

	// 用户投票信息
	function join()
	{
		$token = get_token();
		$opts_ids = array_filter(I('post.optArr'));

		$vote_id = intval($_POST ["vote_id"]);

		$info = $this->_getVoteInfo($vote_id);
		if (empty($info)) {
			$this->error('不存在该投票');
		}
		if ($info['is_fans'] && $this->mid < 0) {
			$this->error('请先关注微信号才能投票');
		}
		// 检查ID是否合法
		if (empty ($vote_id) || 0 == $vote_id) {
			$this->error("错误的投票ID");
		}
		if ($this->_is_overtime($vote_id)) {
			$this->error("请在指定的时间内投票");
		}
		if ($this->_is_join($vote_id, $this->mid, $token)) {
			$this->error("您已经投过,请不要重复投");
		}
		if (empty ($_POST ['optArr'])) {
			$this->error("请先选择投票项");
		}
		if (count($opts_ids) < $info['min_num']) {
			$this->error("请最少选择 {$info['min_num']} 项");
		}

		// 如果没投过，就添加
		$data ["user_id"] = $this->mid;
		$data ["vote_id"] = $vote_id;
		$data ["token"] = $token;
		$data ["options"] = implode(',', $opts_ids);
		$data ["cTime"] = time();
		$addid = M("power_vote_log")->add($data);
		// 投票选项信息的num+1
		foreach ($opts_ids as $v) {
			$v = intval($v);
			$res = M("power_vote_option")->where('id=' . $v)->setInc("opt_count");
		}

		// 投票信息的vote_count+1
		$res = M("power_vote")->where('id=' . $vote_id)->setInc("vote_count");

		// 增加积分
		add_credit('power_vote');

		redirect(U('result', 'id=' . $vote_id));
	}

	/**
	 * @param $vote_id
	 * @return int 0-正在进行中 1-还未开始 -1-已经结束
	 */
	private function _is_overtime($vote_id) {
		// 先看看投票期限过期与否
		$the_vote = M ( "power_vote" )->where ( "id=$vote_id" )->find ();
		
		if(!empty($the_vote['start_date']) && $the_vote ['start_date'] > NOW_TIME) return 1;
		
		$deadline = $the_vote ['end_date'] + 86400;
		if(!empty($the_vote['end_date']) && $deadline <= NOW_TIME) return -1;
		
		return 0;
	}
	private function _is_join($vote_id, $user_id, $token) {
		// $vote_limit = M ( 'vote' )->where ( 'id=' . $vote_id )->getField ( 'vote_limit' );
		$vote_limit = 1;
		$list = M ( "power_vote_log" )->where ( "vote_id=$vote_id AND user_id='$user_id' AND token='$token' AND options <>''" )->select ();
		$count = count ( $list );
		$info = array_pop ( $list );
		if ($info) {
			$joinData = explode ( ',', $info ['options'] );
			$this->assign ( 'joinData', $joinData );
		}
		if ($count >= $vote_limit) {
			return true;
		}
		return false;
	}
	private function inject_can_join($vote_id) {
		$open_id = get_openid();
		$token = get_token();
		$this -> overtime = $this -> _is_overtime($vote_id);
		$this -> beforeJoined = !!$this -> _is_join($vote_id, $this -> mid, $token);
		$this -> joinAccess = $open_id!=-1 && $token!=-1;
		$this -> canJoin = !$this -> beforeJoined && $this -> joinAccess && $this -> overtime == 0;
	}
}
