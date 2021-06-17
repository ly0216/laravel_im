<?php

/**
 * 校验是否为合法的json格式
 * @param $obj
 * @return mixed
 */
function jsonSync($obj)
{
    return json_decode(json_encode($obj), true);
}

/**
 * 格式化数字, 删除多余的0
 * @param $number
 * @return mixed|string
 */
function del0($number)
{
    $number = trim(strval($number));
    if (preg_match('#^-?\d+?\.0+$#', $number)) {
        return preg_replace('#^(-?\d+?)\.0+$#', '$1', $number);
    }
    if (preg_match('#^-?\d+?\.[0-9]+?0+$#', $number)) {
        return preg_replace('#^(-?\d+\.[0-9]+?)0+$#', '$1', $number);
    }

    return $number;
}

/**
 * 校验邮箱格式
 * @param $email
 * @return bool
 */
function validEmail($email)
{
    return !!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/", $email);
}

/**
 * 校验手机号格式
 * @param $mobile
 * @return bool
 */
function validMobile($mobile)
{
    return !!preg_match("/^1[345789]{1}\d{9}$/", $mobile);
}

/**
 * 获取随机字符串
 * @param $length
 * @param bool $specialChars
 * @return string
 */
function randString($length, $specialChars = false)
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    if ($specialChars) {
        $chars .= '!@#$%^&*()';
    }
    $result = '';
    $max = strlen($chars) - 1;
    for ($i = 0; $i < $length; $i++) {
        $result .= $chars[rand(0, $max)];
    }
    return $result;
}

/**
 * 错误返回
 * @param string $msg
 * @param int $code
 * @param array $data
 * @return mixed
 */
function error($msg = 'fail', $data = [])
{
    $res['code'] = 1;
    $res['message'] = $msg;
    if ($data) {
        $res['data'] = $data;
    }
    return $res;
}

/**
 * 成功返回
 * @param array $data
 * @param string $msg
 * @return mixed
 */
function success($msg = 'success', $data = [])
{
    if (is_string($data)) {
        $msg = $data;
        $data = [];
    }
    $res['code'] = 0;
    $res['message'] = $msg;
    $res['data'] = $data;
    return $res;
}

/**
 * 数值转字符串
 * @param $num
 * @return float|mixed|string
 */
function num2str($num)
{
    // 判断是否是数值
    if (!is_numeric($num)) {
        return 0;
    }
    //  判断是否是科学记数法
    if (!stripos($num, 'e')) {
        return $num;
    }
    $numArr = explode('e', strtolower($num));
    if ($numArr[1] > 0) {
        $num = trim(preg_replace('/[=\'"]/', '', $num, 1), '"'); //出现科学计数法，还原成字符串
        $result = "";
        while ($num > 0) {
            $v = $num - floor($num / 10) * 10;
            $num = floor($num / 10);
            $result = $v . $result;
        }
        return del0($result);
    } else {
        $a = explode("e", strtolower($num));
        return del0(bcmul($a[0], bcpow(10, $a[1], 10), 10));
    }
}


/**
 * 生成图形验证码
 * @param int $length
 * @return string
 */
function captchaCode($length = 4)
{
    // 密码字符集，可任意添加你需要的字符
    $chars = '2346789abcdefghjmnpqrtuxyzABCDEFGHJMNPQRTUXYZ';

    $code = '';
    for ($i = 0; $i < $length; $i++) {
        // 这里提供两种字符获取方式
        // 第一种是使用 substr 截取$chars中的任意一位字符；
        // 第二种是取字符数组 $chars 的任意元素
        // $code .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        $code .= $chars[mt_rand(0, strlen($chars) - 1)];
    }

    return strtolower($code);
}

/**
 * 获取hash
 * @param $number
 * @return bool|string
 */
function get_hash($number)
{
    $return = substr(bin2hex($number), 0, 4);

    if (strlen($return) < 4) {
        $return = str_pad($return, 4, "0");
    }
    return $return;
}

/**
 * 随机cmid
 * @param $num
 * @return string
 */
function cmid($num)
{
    $a = new CustomDriver();
    return $a->encode($num);
}


/**
 * 图片地址
 * @param $path
 * @return string
 */
function ImgUrl($path)
{
    $pre = "https://cm-web.oss-cn-beijing.aliyuncs.com/";
    return strstr($path, $pre) ? $path : $pre . $path;
}


/**
 * 获取ip
 * @return array
 */
function get_all_headers()
{
    $headers = [];
    foreach ($_SERVER as $name => $value) {
        if (substr($name, 0, 5) == 'HTTP_') {
            $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
        }
    }
    return $headers;
}

/**
 * 获取真实IP
 * @return string
 */
function get_client_ip()
{
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if (isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

/**
 * 只显示姓氏
 * @param $name
 * @return string
 */
function hideName($name)
{
    $len = mb_strlen($name);
    $sur = mb_substr($name, 0, 1);
    $suf = '';
    for ($i = 1; $i < $len; $i++) {
        $suf .= '*';
    }
    return $sur . $suf;
}

/**
 * 隐藏身份证号
 * @param $cardNum
 * @return string
 */
function hideCardNum($cardNum)
{
    $suf = '';
    for ($i = 0; $i < strlen($cardNum) - 8; $i++) {
        $suf .= '*';
    }
    $cardNum_asterisk = substr($cardNum, 0, 4) . $suf . substr($cardNum, -4, 4);
    return $cardNum_asterisk;
}

/**
 * 隐藏手机号
 * @param $mobile
 * @return string
 */
function hideMobile($mobile)
{
    $arr = str_split($mobile);
    $str = '';
    for ($i = 0; $i < strlen($mobile); $i++) {
        if ($i > 0) {
            if ($i % 2 == 0) {
                $str .= "*";
            } else {
                $str .= $arr[$i];
            }
        } else {
            $str .= $arr[$i];
        }
    }
    return $str;
}

/**
 * 校验是否为正确的json格式
 * @param $json
 * @return bool
 */
function isJson($json)
{
    json_decode($json);
    return (json_last_error() == JSON_ERROR_NONE);
}

/**
 * 获取订单编号
 * @return string
 */
function getOrderSn()
{
    $yCode = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
    $date_str = date("YmdHis");
    $y = substr($date_str, 0, 4);
    $m = substr($date_str, 4, 2);
    $d = substr($date_str, 6, 2);
    $orderSn = $yCode[intval($y) - 2020] . strtoupper(dechex($m)) . $d . '-' . substr(time(), -4) . '-' . substr(microtime(), 2, 6) . '-' . sprintf('%02d', mt_rand(0, 99));
    return $orderSn;
}

/**
 * xml转型为数组
 * @param $xml
 * @return mixed
 */
function xmlTransformArray($xml)
{
    libxml_disable_entity_loader(true);
    $obj = simplexml_load_string($xml, "SimpleXMLElement", LIBXML_NOCDATA);
    return json_decode(json_encode($obj), true);
}

/**
 * 拼接Url地址字符串
 * @param $data
 * @return string
 */
function ToUrlParams($data)
{
    $buff = "";
    foreach ($data as $k => $v) {
        if ($k != "sign" && $v != "" && !is_array($v)) {
            $buff .= $k . "=" . $v . "&";
        }
    }

    $buff = trim($buff, "&");
    return $buff;
}
