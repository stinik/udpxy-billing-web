<?php

class TariffsController extends CController{

    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }
    public function accessRules()
    {
        return array(
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions'=>array('index','detail'),
                'users'=>array('@'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public function actionIndex(){
        if ((isset($_POST['newName'])&&($_POST['newName'])!='')&&
        (isset($_POST['newDescr'])&&($_POST['newDescr'])!='')){
            $tariff= new Tvpack();
            $tariff->name=$_POST['newName'];
            $tariff->descr=$_POST['newDescr'];
            if ($tariff->save())
                $this->redirect(array("index"));
        }


        $tariffs = Tvpack::model()->findAll();
        $this->render('index',array('tariffs'=>$tariffs));
    }
    public function actionDetail(){

        //Add free channels to tvpack
        if (isset($_POST['chaddids'])&&(count($_POST['chaddids'])>0)){
            foreach ($_POST['chaddids'] as $chid){
                $tvaccos=new TvpackList();
                $tvaccos->id_channel=(int)$chid;
                $tvaccos->id_tvpack=(int)$_GET['id'];
                $tvaccos->save();
            }
        }

        //Добавление каналов из плейлиста SevStar
        if (isset($_FILES['playlistfile'])){
            $sevstarplaylist = new SevStarPlaylist();
            $channels=$sevstarplaylist->Parse($_FILES['playlistfile']['tmp_name']);
            foreach ($channels as $channel){
                $channel_id=Channels::model()->AddChannel($channel['name'],$channel['type'],$channel['address'],json_encode(array()));
                TvpackList::model()->AddChannel($_POST['tariffId'],$channel_id);
            }
        }

        //Очистка списка каналов в тарифе
        if (isset($_POST['clearchannels'])&&($_POST['clearchannels']==1)){
            TvpackList::model()->deleteAll('id_tvpack=:id_tvpack',array(':id_tvpack'=>(int)$_POST['tariffId']));
        }

        //Удаление канала из тарифа
        if (isset($_POST['deleteChId'])&&($_POST['deleteChId'])!=''){
            TvpackList::model()->deleteAllByAttributes(array('id_channel'=>(int)$_POST['deleteChId']));
        }

        $tariff=Tvpack::model()->with(array('channels'))->findByPk((int)$_GET['id']);
        if ($tariff==NULL) $this->redirect(array('index'));

        $criteria= new CDbCriteria();
		$criteria->order="ch_name ASC";
        $freeChannels=Channels::model()->findAll($criteria);
        $chlist=CHtml::listData($freeChannels,'id','ch_name');

        $criteria= new CDbCriteria();
        $criteria->condition="id_tvpack=:id_tvpack";
        $criteria->params=array(':id_tvpack'=>(int)$_GET['id']);
        $criteria->select='id_channel';
        $channels_tvpack = TvpackList::model()->findAll($criteria);

        foreach ($channels_tvpack as $del_channel){
            unset($chlist[$del_channel->id_channel]);
        }

        //----------------Управление тарифом----------------
        if (isset($_POST['newName'])&&($_POST['newName'])!=''){
            $tariff->name=$_POST['newName'];
            if($tariff->save())
                $this->redirect(array('detail','id'=>$tariff->id));
        }
        if (isset($_POST['newDescr'])&&($_POST['newDescr'])!=''){
            $tariff->descr=$_POST['newDescr'];
            if($tariff->save())
                $this->redirect(array('detail','id'=>$tariff->id));
        }
        if (isset($_POST['deleteTid'])&&($_POST['deleteTid'])!=''){
            if ((int)$_POST['deleteTid']==$tariff->id)
                if ($tariff->delete()){
                    TvpackList::model()->deleteAllByAttributes(array('id_tvpack'=>$tariff->id));
                    $this->redirect(array("index"));
                }
        }

        $this->render('detail',array('tariff'=>$tariff,'chlist'=>$chlist));
    }

} 