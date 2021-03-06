<?php

namespace Addons\PowerVote;
use Common\Controller\Addon;

/**
 * 投票插件
 * @author XGHeaven
 */

    class PowerVoteAddon extends Addon{

        public $info = array(
            'name'=>'PowerVote',
            'title'=>'Power投票',
            'description'=>'这是一个增强的投票插件',
            'status'=>1,
            'author'=>'XGHeaven',
            'version'=>'1.1',
            'has_adminlist'=>1,
            'type'=>1
        );

	public function install() {
		$install_sql = './Addons/PowerVote/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/PowerVote/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }