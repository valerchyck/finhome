<?php
namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class LoginForm extends Model {
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = false;

    public function rules() {
        return [
            [['username',  'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password',   'validatePassword'],
        ];
    }

	public function attributeLabels() {
		return [
			'username' => 'Логин',
			'password' => 'Пароль',
		];
	}

	public function validatePassword($attribute, $params) {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    public function login() {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
        }

        return false;
    }

    public function getUser() {
        if ($this->_user === false) {
            $this->_user = User::findOne(['login' => $this->username]);
        }

        return $this->_user;
    }
}
