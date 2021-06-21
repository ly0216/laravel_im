<?php

namespace App\Mongodb;

use Illuminate\Support\Facades\DB;
use Jenssegers\Mongodb\Eloquent\Model;

class MemberFd extends Model
{
    //
    protected $connection = 'mongodb';//连接
    //use SoftDeletes;
    protected $collection = 'txzh_member_fd';//文档名---可以理解为表名
    protected $primaryKey = '_id';    //设置 默认使用mongodb的_id 如想想使用自己的ID 可以设置此项


    const CONNECTION = 'mongodb';
    const COLLECTION = 'txzh_member_fd';

    protected $type = [
        'user_id' => 'integer',
        'fd_id' => 'integer',
        'is_delete' => 'integer',
        'created_at' => 'integer',
        'updated_at' => 'integer',
    ];

    const DELETE_ON = 1;//已删除
    const DELETE_OFF = 0;//未删除

    /**
     * 用户ID与socketId绑定
     * @param $user_id
     * @param $fd
     * @return bool
     */
    public function setMemberFd($user_id, $fd)
    {
        try {
            $model = DB::connection($this->connection)->collection($this->collection);
            $has = $model->where('user_id', $user_id)->first();
            $at = time();
            if (!$has) {
                $res = $model->insert([
                    'user_id' => intval($user_id),
                    'fd_id' =>intval($fd),
                    'is_delete' => self::DELETE_OFF,
                    'created_at' => $at,
                    'updated_at' => $at
                ]);
                if (!$res) {
                    return false;
                }
            } else {
                $res = $model->where('user_id', $user_id)->update([
                    'fd_id' => $fd,
                    'updated_at' => $at
                ]);
                if (!$res) {
                    return false;
                }
            }
            return $model;
        } catch (\Exception $exception) {
            return false;
        }

    }


    /**
     * 获取fd列表
     * @param $ids
     * @return mixed
     */
    public static function getFd($ids)
    {
        $model = DB::connection(self::CONNECTION)->collection(self::COLLECTION);
        $list = $model->select('fd_id')->whereIn('user_id', $ids)->get();
        return $list;
    }
}
