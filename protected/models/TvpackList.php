<?php

/**
 * This is the model class for table "tvpack_list".
 *
 * The followings are the available columns in table 'tvpack_list':
 * @property string $id
 * @property string $id_tvpack
 * @property string $id_channel
 */
class TvpackList extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tvpack_list';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id_tvpack, id_channel', 'required'),
			array('id_tvpack, id_channel', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, id_tvpack, id_channel', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'id_tvpack' => 'Id Tvpack',
			'id_channel' => 'Id Channel',
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
		$criteria->compare('id_tvpack',$this->id_tvpack,true);
		$criteria->compare('id_channel',$this->id_channel,true);

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

    /**
     * Add channel to TVPack
     * @param $tariff_id TVPack ID
     * @param $channel_id Channel ID
     * @return null|string NULL or ID
     */
    public function AddChannel($tariff_id,$channel_id){
        $channel=$this->findByAttributes(array('id_tvpack'=>(int)$tariff_id, 'id_channel'=>(int)$channel_id));
        if ($channel!=NULL){
            return $channel->id;
        }else{
            $newPackAssoc = new TvpackList();
            $newPackAssoc->id_channel = (int)$channel_id;
            $newPackAssoc->id_tvpack = (int)$tariff_id;
            if($newPackAssoc->save())
                return $newPackAssoc->id;
            else return NULL;
        }
    }
}
