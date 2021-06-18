<?php
/**
 * Created by PhpStorm.
 * User: liyang
 * Date: 2021/6/15
 * Time: 8:35 PM
 */

namespace App\Services;

use App\Common\Code;
use App\Im\Common;
use Hhxsv5\LaravelS\Swoole\WebSocketHandlerInterface;
use Illuminate\Support\Facades\Log;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

/**
 * @see https://www.swoole.co.uk/docs/modules/swoole-websocket-server
 */
class WebSocketService implements WebSocketHandlerInterface
{
    // Declare constructor without parameters
    public function __construct()
    {

    }
    // public function onHandShake(Request $request, Response $response)
    // {
    // Custom handshake: https://www.swoole.co.uk/docs/modules/swoole-websocket-server-on-handshake
    // The onOpen event will be triggered automatically after a successful handshake
    // }
    public function onOpen(Server $server, Request $request)
    {
        // Before the onOpen event is triggered, the HTTP request to establish the WebSocket has passed the Laravel route,
        // so Laravel's Request, Auth information are readable, Session is readable and writable, but only in the onOpen event.
        // \Log::info('New WebSocket connection', [$request->fd, request()->all(), session()->getId(), session('xxx'), session(['yyy' => time()])]);
        // The exceptions thrown here will be caught by the upper layer and recorded in the Swoole log. Developers need to try/catch manually.

        //$receive_data = $request->getData();
        //$push_data = Common::checkData($request->fd, $receive_data);
        $push_data = [
            'code' => Code::HTTP_SUCCESS,
            'message' => 'Connection successful',
            'data' => [
                'type' => 'connection'
            ]
        ];
        $server->push($request->fd, json_encode($push_data));
    }

    public function onMessage(Server $server, Frame $frame)
    {
        $fd = $frame->fd;
        $receive_data = $frame->data;
        $push_data = Common::checkData($fd, $receive_data);
        $server->push($frame->fd, $push_data);
    }

    public function onClose(Server $server, $fd, $reactorId)
    {
        // The exceptions thrown here will be caught by the upper layer and recorded in the Swoole log. Developers need to try/catch manually.
    }
}
