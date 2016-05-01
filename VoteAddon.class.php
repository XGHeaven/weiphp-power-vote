<?php

namespace Addons\Vote;
use Common\Controller\Addon;

/**
 * 投票插件
 * @author 画城科技
 */

    class VoteAddon extends Addon{

        public $info = array(
            'name'=>'Vote',
            'title'=>'投票',
            'description'=>'这是一个投票插件',
            'status'=>1,
            'author'=>'画城科技',
            'version'=>'0.1',
            'has_adminlist'=>1,
            'type'=>1         
        );

	public function install() {
		$install_sql = './Addons/Vote/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/Vote/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }