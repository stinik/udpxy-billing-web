<?php
class MyController extends CController{

    public function actionIndex(){

        //Select all active orders and allowed for current client
        $active_orders=Orders::model()->GetActiveOrders(Yii::app()->request->getUserHostAddress(), Orders::GET_ONLYUSER);
        $active_allowed=Orders::model()->GetActiveOrders(Yii::app()->request->getUserHostAddress(),Orders::GET_ONLYALLOWED);
        //Get client info by address
        $userinfo=Clients::model()->GetClientByIP(Yii::app()->request->getUserHostAddress());
        $this->render('index',array('allowed_list'=>$active_orders,'orders'=>$active_allowed,'user'=>$userinfo));
    }

    public function actionGetPlaylist(){

        $packlist=array();

        $criteria=new CDbCriteria();
        $criteria->addCondition('ip=:claddr');
        $criteria->params=array(':claddr'=>$this->GetRealIp());
        $user=Clients::model()->find($criteria);

        $criteria=new CDbCriteria();
        $criteria->select='id_tvpack';
        $criteria->addCondition('start_date<=NOW() AND end_date>=NOW()');
        $criteria->compare('status',1);
        $criteria->compare('id_user',$user->id);
        $criteria->compare('id_allowed',0);
        $orders=Orders::model()->findAll($criteria);
        foreach($orders as $order){ $packlist[]=$order->id_tvpack; }

        $criteria=new CDbCriteria();
        $criteria->select=array('id_tvpack');
        $criteria->addCondition('INET_ATON(allowed.ip_start)<=:claddr');
        $criteria->addCondition('INET_ATON(allowed.ip_end)>=:claddr');
        $criteria->params=array(':claddr'=>ip2long($this->GetRealIp()));
        $criteria->with=array('allowed');
        $criteria->together=true;
        $allowed_list=Orders::model()->findAll($criteria);
        foreach($allowed_list as $allowed){ $packlist[]=$allowed->id_tvpack; }
        $packlist=array_unique($packlist);

        $channelslist=array();
        foreach ($packlist as $packid){
            $channels=Tvpack::model()->with(array('channels'))->findByPk($packid)->channels;
            foreach ($channels as $channel){
                $channelslist[$channel->id]=$channel;
            }
        }


        //ob_start();

echo "#EXTM3U cache=1000 deinterlace=7 url-tvg=\"http://tv.sevstar.net/tvprog.zip\" tvg-shift=0 m3uautoload=1
";
        $pstr='';
    foreach ($channelslist as $channel){
        foreach (json_decode($channel->params) as $pkey=>$param)
            $pstr.=$pkey.'="'.$param.'" ';

echo '
#EXTINF:-1 '.$pstr.' tvg-name="'.str_replace(' ','_',$channel['ch_name']).'" tvg-logo="'.$channel['ch_name'].'" ,'.$channel['ch_name'].'
http://'.Yii::app()->params['udpxy_host'].':'.Yii::app()->params['udpxy_port'].'/udp/'.$channel['m_ip'].':'.$channel['m_port'];
$pstr='';
    }

        //ob_end_flush();



    }
} 