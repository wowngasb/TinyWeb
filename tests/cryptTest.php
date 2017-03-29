<?php

const CRYPT_KEY = 'adadwwd';

function safe_base64_encode($str) {
    $str = rtrim(strtr(base64_encode($str), '+/', '-_'), '='); 
    return $str;
}

function safe_base64_decode($str) {
    $str = strtr(trim($str), '-_', '+/');
    $last_len = strlen($str) % 4;
    $str = $last_len==2 ? $str . '==' : ($last_len==3 ? $str . '=' : $str);
    $str = base64_decode($str); 
    return $str;
}

function encrypt($string, $expiry=0) {
    return authcode($string, 'ENCODE', CRYPT_KEY, $expiry);
}

function decrypt($string) {
    return authcode($string, 'DECODE', CRYPT_KEY);
}

function authcode($string, $operation, $key, $expiry=0) {
    $ckey_length = 4;// 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
    $key = md5($key);// 密匙
    $keya = md5(substr($key, 0, 16));// 密匙a会参与加解密
    $keyb = md5(substr($key, 16, 16));// 密匙b会用来做数据完整性验证
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';// 密匙c用于变化生成的密文
    $cryptkey = $keya.md5($keya.$keyc);// 参与运算的密匙
    $key_length = strlen($cryptkey);
    // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)， 
    //解密时会通过这个密匙验证数据完整性
    // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
    $string = $operation == 'DECODE' ? safe_base64_decode(substr($string, $ckey_length)) : pack('L', $expiry>0 ? $expiry + time() : 0). hex2bin(substr(md5($string.$keyb), 0, 16)).$string;
    $string_length = strlen($string);
    $result = '';
    $box = range(0, 255);
    $rndkey = array();
    // 产生密匙簿
    for($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }
    // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度
    for($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }
    // 核心加解密部分
    for($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        // 从密匙簿得出密匙进行异或，再转成字符
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    if($operation == 'DECODE') {  
        // 验证数据有效性，请看未加密明文的格式
        $time = unpack('L', substr($result, 0, 4))[1];
        $string = substr($result, 12);
        if(($time == 0 || $time > time()) && bin2hex(substr($result, 4, 8)) == substr(md5($string.$keyb), 0, 16)) {
            return $string;
        } else {
            return '';
        }
    } else {
        return $keyc . safe_base64_encode($result);
    }
}

$test = encrypt('123456');
echo strlen($test) . "\n";
echo $test . "\n";
echo decrypt($test);
