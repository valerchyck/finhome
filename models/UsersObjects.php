<?php
namespace app\models;

use Yii;

/**
 * This is the model class for table "users_objects".
 *
 * @property integer $id_user
 * @property integer $id_list
 * @property User[] $users
 * @property ObjectsList[] $objects
 */
class UsersObjects extends \yii\db\ActiveRecord {
    public static function tableName() {
        return 'users_lists';
    }

    public function rules() {
        return [
            [['id_user', 'id_list'], 'required'],
            [['id_user', 'id_list'], 'integer'],
        ];
    }

    public function attributeLabels() {
        return [
            'id_user' => 'Пользователь',
            'id_list' => 'Объект',
        ];
    }

	public function getUsers() {
		return $this->hasMany(User::className(), ['id' => 'id_user']);
	}

	public function getObjects() {
		return $this->hasMany(ObjectsList::className(), ['id' => 'id_list']);
	}
}
