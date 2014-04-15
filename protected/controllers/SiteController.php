<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		$this->render('index');
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$name='=?UTF-8?B?'.base64_encode($model->name).'?=';
				$subject='=?UTF-8?B?'.base64_encode($model->subject).'?=';
				$headers="From: $name <{$model->email}>\r\n".
					"Reply-To: {$model->email}\r\n".
					"MIME-Version: 1.0\r\n".
					"Content-Type: text/plain; charset=UTF-8";

				mail(Yii::app()->params['adminEmail'],$subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	public function actionAuth(){
		/*
		if($_GET['state']=='testOpenID'){
			if ($_GET[code]) {
				$chat = CWechat::model();
				$data = $chat->accessTokenFromCode($_GET[code]);
				$openid = '';
				if($data){
					$openid = $data['oepnid'];

				}
			}		
		}*/
		if ($_GET['code']) {
			// 授权成功
			$chat = CWeChat::model();
			$data = $chat -> accessTokenFromCode($_GET[code]);
			if ($data['access_token'] && $data['openid']) {
				$userinfo = $chat->userInfo($data['access_token']);		
				if ($userinfo['openid']) {
					Yii::app()->user->login($userinfo['nickname'],3600);
					$this->redirect(Yii::app()->user->returnUrl);
				}else{
					echo "获取信息失败";
					print_r($userinfo);
					exit;
				}
			}
		}else{
			// 拒绝授权
			echo CHtml::link('返回首页',$this->createUrl('index'));
		}
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		//检查该用户是否授权过


		$chat = CWeChat::model();
	
		$url = $chat->makeAuthUrl('snsapi_userino',Yii::app()->request->getHostInfo().$this->createUrl('auth'),'testOpenID');
		
		$this->redirect($url);
		/*
		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form
		$this->render('login',array('model'=>$model));
		*/
	}
	
	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
}