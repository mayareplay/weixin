<?php
class CWeChat{
	protected $appID;
	protected $appsecret;
	public static $self = null;

	public function __construct($appID=null,$appsecret = null){
		if ($appID == null || $appsecret == null) {
			$this->appID = Yii::app()->params['wechat']['appID'];
			$this->appsecret = Yii::app()->params['wechat']['appsecret'];
		}else{
			$this->appID = $appID;
			$this->appsecret = $appsecret;
		}
	}

	public static function model(){

		if (self::$self == null) {
			self::$self = new self;
		}
		return self::$self;
	}

	/*
	@param string $scope  snsapi_userinfo|snsapi_base
	@return  stirng authUrl
	*/

	public function makeAuthUrl($scope = 'snsapi_userinfo',$returnUri=null,$status='STATUS'){
		if ($returnUri == null) {
			$returnUri = Yii::app()->params['wechat']['authReturnUri'];
		}
		$returnUri = urlencode($returnUri);
		$appID = $this->appID;
		return  "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$appID}&redirect_uri={$returnUri}&response_type=code&scope={$scope}&state={$status}#wechat_redirect";
	}

	/*
	@param string $code
	@return array $data

	suceess:
	{
	   "access_token":"ACCESS_TOKEN",
	   "expires_in":7200,
	   "refresh_token":"REFRESH_TOKEN",
	   "openid":"OPENID",
	   "scope":"SCOPE"
	}
	error:
	{"errcode":40029,"errmsg":"invalid code"}
	*/
	public function accessTokenFromCode($code){
		$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$this->appID."&secret=".$this->appsecret."&code={$code}&grant_type=authorization_code";
		return $this->getUrlData($url);
	}

	public function refresh_token($refresh_token){
		$url = "https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=".$this->appID."&grant_type=refresh_token&refresh_token=$refresh_token";
		return $this->getUrlData($url);
	}

	public function userInfo($access_token,$openid){
		$url = "https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$openid}&lang=zh_CN";
		return $this->getUrlData($url);
	}

	public function getUrlData($url){
		$cnt = file_get_contents($url);
		if($cnt){
			return json_decode($cnt);
		}
	}

}
?>