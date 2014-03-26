<?php
/**
 * minified. 2014
 * @author Eduard Maksimovich <edward.vstock@gmail.com>
 *
 * Class: ApiController
 */

namespace api\components;


use yii\web\Controller;

class ApiController extends Controller {

	const API_NO_ERRORS                         = 0x0;
	const API_ERROR_USER_NOT_FOUND              = 0x1;
	const API_ERROR_USER_TOKEN_DOES_NOT_MATCH   = 0x2;
	const API_WARNING_USER_DATA_NOT_FOUND       = 0x3;
	const API_ERROR_SOURCE_NOT_FOUND            = 0x4;
	const API_ERROR_SOURCE_UNKNOWN_ERROR        = 0x5;

	protected function setJsonResponse($data = []) {
		header('Content-type: text/json; charset=UTF-8');
		return json_encode($data);
	}

	public function init() {
		\Yii::$app->request->enableCsrfValidation = false;
		\Yii::$app->request->enableCookieValidation = false;
		parent::init();
	}

} 