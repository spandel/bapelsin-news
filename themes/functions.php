<?php

	function login_menu2()
	{
		$menu="";
		$bap=CBapelsin::instance();
		
		if($bap->user->isAuthenticated())
		{
			$user=$bap->user->getUserProfile();
			$menu="<a href='http://gravatar.com/site/signup/'><img class='gravatar' src='".get_gravatar(20)."'></a>";
			$menu.="<a href='{$bap->request->createUrl('user/profile')}'>{$user['acronym']}</a> ";
			if($bap->user->isAdministrator())
			{
				$menu.="<a href='{$bap->request->createUrl('news/authoring')}'>författning</a> ";
			}
			$menu.="<a href='{$bap->request->createUrl('user/logout')}'>logout</a>";
		}
		else
		{
			$menu="<a href='{$bap->request->createUrl('user/login')}'>Login</a>";
		}		
		return $menu;
	}
