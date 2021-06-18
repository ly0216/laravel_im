<?php

namespace App\Mongodb;

use Illuminate\Support\Facades\DB;
use Jenssegers\Mongodb\Eloquent\Model;

class MemberFd extends Model
{
    //
    protected $connection = 'mongodb';
    //use SoftDeletes;
    protected $collection = 'txzh_member_fd';    //文档名
    protected $primaryKey = '_id';    //设置id
    protected $guarded = ['user_id', 'fd_id', 'is_delete', 'time'];  //设置字段白名单
    protected $dates = ['deleted_at'];

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
            if (!$has) {
                $res = $model->insert([
                    'user_id' => $user_id,
                    'fd_id' => $fd,
                    'is_delete' => 0,
                    'time' => time()
                ]);
                if (!$res) {
                    return false;
                }
            } else {
                $res = $model->where('user_id', $user_id)->update([
                    'fd_id' => $fd,
                    'time' => time()
                ]);
                if (!$res) {
                    return false;
                }
            }
            return true;
        } catch (\Exception $exception) {
            return false;
        }

    }


    /**
     * 获取fd列表
     * @param $ids
     * @return mixed
     */
    public function getFd($ids)
    {
        $model = DB::connection($this->connection)->collection($this->collection);
        $list = $model->select('fd_id')->whereIn('user_id', $ids)->get();
        return $list;
    }
}
