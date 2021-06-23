<?php

namespace App\Im;

use Illuminate\Database\Eloquent\Model;
use App\Mongodb\ChatMember;
use App\Mongodb\MemberFd;
use App\User;
class GeTuiPush extends Model
{
    //
    /**
     * 个推消息--推送
     * @param $user_id
     * @param $to_user_id
     * @param $type
     * @param $data
     * @return bool
     */
    public static function sendMessageToUid($user_id,$to_user_id,$type,$data){


        //校验是否在线
        $is_online = ChatMember::select('is_onLine')
            ->where('user_id',intval($to_user_id))->where('list_id',$data['list_id'])->first();
        if(!$is_online){
            return false;
        }
        if($is_online->is_onLine == 0){
            return false;
        }
        //通过用户ID 换取fd
        $fd = MemberFd::select('fd_id')->where('user_id',intval($to_user_id))->first();
        if(!$fd){
            return false;
        }
        $fd_id = $fd->fd_id;
        if($type != 'chatData'){
            return false;
        }
        $type = $data['data']['msg']['type'];
        $content = '';
        if(isset($data['data']['msg']['content']->text)){
            $content = $data['data']['msg']['content']->text;
        }

        if($type >= 21 && $type <= 29){
            //直播间消息不推送
            return false;
        }
        /* if($type == 'chatData' && isset($data['list_id'])){
             //查询是否是直播间
             $message_type = ChatGroup::select('list_id,is_live,name')
                 ->where('list_id' , $data['list_id'])->first();

             if($message_type){
                 if($message_type->is_live == 1){
                     return;
                 }else{
                     //不是直播的群聊  录入数据
                     $bool = ImMessageData::create([
                         'user_id' => $user_id,
                         'push_user' => USER_ID,
                         'name' => $name,
                         'type' => $type,
                         'list_id' => $data['list_id'],
                         'title' => $message_type['name'],
                         'body' => $content,
                         'created_at' => date('Y-m-d H:i:s' , time())
                     ]);

                     if($bool){
                         return;
                     }
                 }
             }
         }*/

        //查询用户的信息
        $nickname = '';
        $user = User::getOne($user_id);
        $to_user = User::getOne($to_user_id);

        if(!$user || !$to_user){
            return false;
        }
        $messageData = Common::messageType($type,$user->nickname,$content);
        return $fd_id;
    }
}
