<?php

const CRYPT_KEY = '5d4&Y&^$$SSEfdaFfseF$Df%#Gg345DkkhDo^&34%@E';

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

function rand_str($length){
    $str = '';
    $tmp_str = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
    $max = strlen($tmp_str)-1;
    for($i=0; $i<$length; $i++){
        $str .= $tmp_str[ rand(0, $max) ];   //rand($min,$max)生成介于min和max两个数之间的一个随机整数
    }
    return $str;
}
  
function authcode($string, $operation, $key, $expiry=0) {
    $ckey_length = 2;// 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
    $key = md5($key);// 密匙
    $keya = md5(substr($key, 0, 16));// 密匙a会参与加解密
    $keyb = md5(substr($key, 16, 16));// 密匙b会用来做数据完整性验证
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): rand_str(2) ): '';// 密匙c用于变化生成的密文
    $cryptkey = $keya.md5($keya.$keyc);// 参与运算的密匙
    $key_length = strlen($cryptkey);
    // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)， 
    //解密时会通过这个密匙验证数据完整性
    // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
    $string = $operation == 'DECODE' ? safe_base64_decode(substr($string, $ckey_length)) : pack('L', $expiry>0 ? $expiry + time() : 0). hex2bin(substr(md5($string.$keyb), 0, 8)).$string;
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
        $string = substr($result, 8);
        if(($time == 0 || $time > time()) && bin2hex(substr($result, 4, 4)) == substr(md5($string.$keyb), 0, 8)) {
            return $string;
        } else {
            return '';
        }
    } else {
        return $keyc . safe_base64_encode($result);
    }
}

$test = encrypt('1234567890');
echo strlen($test) . "\n";
echo $test . "\n";
echo decrypt($test) . "\n";
echo "\n";

$test = encrypt('1');
echo strlen($test) . "\n";
echo $test . "\n";
echo decrypt($test) . "\n";
echo "\n";

$test = encrypt(1234567890);
echo strlen($test) . "\n";
echo $test . "\n";
echo decrypt($test) . "\n";
echo "\n";

$test = encrypt( pack('L', 1234567890) );
echo strlen($test) . "\n";
echo $test . "\n";
echo unpack('L', decrypt($test))[1] . "\n";
echo "\n";

$test = encrypt( pack('L', 1) );
echo strlen($test) . "\n";
echo $test . "\n";
echo unpack('L', decrypt($test))[1] . "\n";
echo "\n";
