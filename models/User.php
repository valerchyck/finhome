<?php
namespace app\models;

use Yii;
use yii\db\Exception;
use yii\db\Query;
use yii\helpers\Url;
use yii\rbac\Role;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $login
 * @property string $password
 * @property string $open_pass
 * @property string $name
 * @property integer $phone
 * @property string $email
 * @property string $skype
 * @property integer $compare
 * @property string $group
 * @property integer $role
 * @property integer $isEdit
 * @property ObjectsList[] $lists
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface {
	public $repeatPassword;
	public $role;

	public static function tableName() {
        return 'user';
    }

    public function rules() {
        return [
            [['id', 'phone', 'compare', 'role', 'isEdit'], 'integer'],
            [['login', 'name', 'phone', 'email', 'skype'], 'required'],
            [['login', 'password', 'group', 'open_pass'], 'string', 'max' => 50],
            [['name'], 'string', 'max' => 255],
            [['email', 'skype'], 'string', 'max' => 100],
	        ['repeatPassword', 'compare', 'compareAttribute' => 'open_pass', 'message' => 'Пароли не совпадают'],
        ];
    }

	public function getLists() {
		return $this->hasMany(ObjectsList::className(), ['id' => 'id_list'])
			        ->viaTable(UsersObjects::tableName(), ['id_user' => 'id']);
	}

	public function afterFind() {
		parent::afterFind();

		$this->role = (new Query())
			->select('item_name')
			->from('auth_assignment')
			->where(['user_id' => $this->id])
			->one()['item_name'] == 'admin';
	}

	public function attributeLabels() {
        return [
            'id'             => 'Ид',
            'login'          => 'Логин',
            'password'       => 'Пароль',
            'repeatPassword' => 'Повторите пароль',
            'name'           => 'Имя',
            'phone'          => 'Телефон',
            'email'          => 'E-Mail',
            'skype'          => 'Skype',
            'compare'        => 'Сравнение',
            'group'          => 'Группа',
            'open_pass'      => 'Пароль',
            'role'           => 'Роль',
            'isEdit'         => 'Редактирование',
        ];
    }

	private function getRule() {
		return $this->role == 1 ? 'admin' : 'user';
	}

	public function __set($name, $value) {
		if ($name == 'role') {
			$this->role = $value;
		}
		else
			parent::__set($name, $value);
	}

	public function afterSave($insert, $changedAttributes) {
		parent::afterSave($insert, $changedAttributes);

		\Yii::$app->authManager->assign(new Role(['name' => $this->getRule()]), $this->id);
	}

	public static function findIdentity($id) {
        return self::findOne(['id' => $id]);
    }

	public function getId() {
		return $this->id;
	}

    public function validatePassword($password) {
        return $this->password === md5($password);
    }

	public function beforeSave($insert) {
		$this->setAttributes([
			'password' => md5($this->open_pass),
			'group'    => $this->getRule(),
		]);

		return parent::beforeSave($insert);
	}

	public static function getMenu(\yii\web\User $user) {
		$menu = [];
        if (!$user->isGuest) {
            array_push($menu, ['label' => 'База', 'url' => '/']);
        }

		if ($user->can('admin')) {
            array_push($menu, ['label' => 'Загрузка', 'url' => '/download']);
            array_push($menu, ['label' => 'Пользователи', 'url' => '/users']);
            array_push($menu, ['label' => 'Объекты', 'url' => '/objects']);
		}
		array_push($menu, ['label' => 'Обратная связь', 'url' => '/feedback']);

		if (!$user->isGuest) {
			array_push($menu, ['label' => 'Выход', 'url' => '/logout']);
		}

		return $menu;
	}

	public function saveAccess($data) {
		UsersObjects::deleteAll(['id_user' => $this->id]);

		$values = [];
		foreach ($data as $value) {
			$values[] = "({$this->id}, $value)";
		}
		$values = implode(',', $values);

		try {
			\Yii::$app->db->createCommand("INSERT INTO `users_lists` VALUES $values")->execute();
			return true;
		}
		catch (Exception $e) {
			return false;
		}
	}

	public static function findIdentityByAccessToken($token, $type = null) {}
    public function getAuthKey() {}
    public function validateAuthKey($authKey) {}
}
