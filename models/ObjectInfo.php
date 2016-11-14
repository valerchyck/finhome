<?php
namespace app\models;

use Yii;

/**
 * This is the model class for table "object_info".
 *
 * @property string $id
 * @property string $id_object
 * @property string $name
 * @property string $count
 * @property Object $object
 */
class ObjectInfo extends \yii\db\ActiveRecord {
    public static function tableName() {
        return 'object_info';
    }

    public function rules() {
        return [
            [['id_object'], 'required'],
            [['id_object'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['count'], 'string', 'max' => 50],
        ];
    }

    public function attributeLabels() {
        return [
            'id'        => 'Ид',
            'id_object' => 'Ид объекта',
            'name'      => 'Наименование',
            'count'     => 'Количество',
        ];
    }

	public function getObject() {
		return $this->hasOne(Object::className(), ['id' => 'id_object']);
	}
}
