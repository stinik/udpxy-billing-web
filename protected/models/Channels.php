<?php

/**
 * This is the model class for table "tvpack_list".
 *
 * The followings are the available columns in table 'tvpack_list':
 * @property string $id
 * @property string $ch_name
 * @property string $m_ip
 * @property integer $m_port
 * @property integer $params
 */
class Channels extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'channels';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('ch_name, m_ip, m_port, params', 'required'),
			array('m_port', 'numerical', 'integerOnly'=>true),
			array('ch_name', 'length', 'max'=>32),
			array('m_ip', 'length', 'max'=>15),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, ch_name, m_ip, m_port, params', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
            'tariffs'=>array(self::MANY_MANY, 'Tariff',
                'tvpack_list(id_channel, id_tvpack)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'ch_name' => 'Ch Name',
			'm_ip' => 'M Ip',
			'm_port' => 'M Port',
            'params'=>'Params',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('ch_name',$this->ch_name,true);
		$criteria->compare('m_ip',$this->m_ip,true);
		$criteria->compare('m_port',$this->m_port);
        $criteria->compare('params',$this->params);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TvpackList the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
