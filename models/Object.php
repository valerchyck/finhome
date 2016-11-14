<?php
namespace app\models;

use Yii;
use yii\base\InvalidParamException;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "object".
 *
 * @property integer $id
 * @property integer $id_list
 * @property string $date
 * @property double $course
 * @property integer $summa
 * @property string $provider
 * @property string $comment
 * @property integer $mark
 * @property ObjectInfo[] $info
 * @property ObjectsList[] $list
 * @property integer $sumResult
 * @property integer $composition
 */
class Object extends \yii\db\ActiveRecord {

	private $correspondence = [
		'Дата'         => 'date',
		'Курс'         => 'course',
		'Сумма'        => 'summa',
		'Метка'        => 'mark',
		'Поставщик'    => 'provider',
		'Наименование' => 'name',
		'Количество'   => 'count',
		'Уточнение'    => 'comment',
	];

    public static function tableName() {
        return 'object';
    }

	public function rules() {
        return [
            [['date', 'summa', 'id_list'], 'required'],
            [['date', 'composition'], 'safe'],
            [['course'], 'number'],
            [['summa', 'id_list', 'mark'], 'integer'],
            [['comment'], 'string'],
            [['provider'], 'string', 'max' => 100],
        ];
    }

    public function attributeLabels() {
        return [
            'id'          => 'Ид',
            'id_list'     => 'Ид списка',
            'date'        => 'Дата',
            'course'      => 'Курс',
            'summa'       => 'Сумма',
            'provider'    => 'Поставщик',
            'comment'     => 'Уточнение',
            'sumResult'   => 'Результат',
            'mark'        => 'М',
            'composition' => 'Результат',
        ];
    }

	public function getInfo() {
		return $this->hasMany(ObjectInfo::className(), ['id_object' => 'id']);
	}

	public function getList() {
		return $this->hasOne(ObjectsList::className(), ['id' => 'id_list']);
	}

	public function search($ids, $params) {
		$query = self::find()
            ->select('`object`.*, (course * summa) as `composition`')
            ->where(['id_list' => $ids])
            ->joinWith('list');

		$dataProvider = new ActiveDataProvider([
			'query'      => $query,
			'pagination' => [
				'pageSize' => \Yii::$app->session->get('page-count') == null ? 20 : \Yii::$app->session->get('page-count'),
			],
		]);

		$dataProvider->sort->attributes['count'] = [
			'asc'  => ['object_info.count' => SORT_ASC],
			'desc' => ['object_info.count' => SORT_DESC],
		];
		$dataProvider->sort->attributes['name'] = [
			'asc'  => ['object_info.name' => SORT_ASC],
			'desc' => ['object_info.name' => SORT_DESC],
		];
		$dataProvider->sort->attributes['list.name'] = [
			'asc'  => ['objects_list.name' => SORT_ASC],
			'desc' => ['objects_list.name' => SORT_DESC],
		];
        $dataProvider->sort->attributes['composition'] = [
            'asc'  => ['composition' => SORT_ASC],
            'desc' => ['composition' => SORT_DESC],
        ];

		if (!$this->load($params)) {
			return $dataProvider;
		}

		$query->joinWith('info');
		foreach ($params['Object'] as $key => $item) {
			if ($item == null)
				continue;

			$table = 'object';
			$attr  = self::getTableSchema()->getColumn($key);
			if (in_array($key, ['name', 'count'])) {
				$table = 'object_info';
				$attr  = ObjectsList::getTableSchema()->getColumn($key);
			}

			if (strpos($item, ';') != false) {
				$words = explode(';', $item);
				$where = [];
				foreach ($words as $value) {
					$value = trim($value);
					$where[] = "($table.$key like '%$value%')";
				}

				$query->andOnCondition(implode(' or ', $where));
			}
			else if (in_array($key, ['date', 'summa'])) {
				$date = explode(' - ', $item);
				$query->andWhere("$table.$key between '{$date[0]}' and '{$date[1]}'");
			}
			else {
				if (is_numeric($attr)) {
					if (strpos($item, '|') != false) {
						$words = explode('|', $item);
						$from = trim($words[0]);
						$to = trim($words[1]);

						$query->andWhere("$table.$key between $from and $to");
					}
					else {
						$query->andWhere("$table.$key like '$item%'");
					}
				}
				else {
                    if ($key == 'composition') {
                        if (strpos($item, '|') != false) {
                            $words = explode('|', $item);
                            $from = trim($words[0]);
                            $to = trim($words[1]);

                            $query->andWhere("(course * summa) between $from and $to");
                        }
                        else {
                            $query->andWhere("(course * summa) like '$item%'");
                        }
                    }
                    else
                        $query->andWhere("$table.$key like '%$item%'");
				}
			}
		}

		return $dataProvider;
	}

	public function save($runValidation = true, $attributeNames = null, array $columns = null) {
		if ($attributeNames !== null && $columns !== null) {
			/**
			 * @var $info ObjectInfo[]
			 */
			$info = [];

			foreach ($attributeNames as $key => $name) {
				if ($key == 1)
					$name = date('Y-m-d', strtotime($name));
				if ($key == 3)
					$name = (int)$name;

				if ($columns[$key] == null)
					continue;

				$realName = $this->correspondence[$columns[$key]];
				if (is_array($name)) {
					foreach ($name as $number => $value) {
						if (!isset($info[$number])) {
							$info[$number] = new ObjectInfo([$realName => $value]);
						}
						else {
							$info[$number]->{$realName} = $value;
						}
					}
				}
				else {
					$this->setAttribute($realName, $name);
				}
			}

			if ($this->save()) {
				foreach ($info as $value) {
					$value->id_object = $this->id;
					if (!$value->save()) {
						throw new InvalidParamException('object info was`n wrote');
					}
				}
			}

			return true;
		}

		return parent::save($runValidation, $attributeNames);
	}

	public function getSumResult() {
		if ($this->course == 0)
			return 0;
		
		return $this->summa / $this->course;
	}

    public function getComposition() {
        return $this->course * $this->summa;
    }

    public function setComposition($value) {
        $this->composition = $value;
    }
}
