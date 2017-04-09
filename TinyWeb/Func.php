<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/10 0010
 * Time: 0:15
 */

namespace TinyWeb;


class Func
{
    public static function hashPassWord($salt, $password)
    {
        return md5(md5($salt . $password));
    }

    /**
     * 从一个数组中提取需要的key  缺失的key设置为空字符串
     * @param array $arr 原数组
     * @param array $need 需要的key 列表
     * @return array 需要的key val数组
     */
    public static function filter_keys(array $arr, array $need)
    {
        $rst = [];
        foreach ($need as $val) {
            $rst[$val] = isset($arr[$val]) ? $arr[$val] : '';
        }
        return $rst;
    }

    /**
     * 检查字符串是否包含指定关键词
     * @param string $str 需检查的字符串
     * @param string $filter_str 关键词字符串 使用 $split_str 分隔
     * @param string $split_str 分割字符串
     * @return bool 是否含有关键词
     */
    public static function pass_filter($str, $filter_str, $split_str)
    {
        $filter = explode($split_str, $filter_str);
        foreach ($filter as $key => $val) {
            $val = trim($val);
            if ($val != '') {
                $test = stripos($str, $val);
                if ($test !== false) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * 在指定时间 上添加N个月的日期字符串
     * @param string $time_str 时间字符串
     * @param int $add_month 需要增加的月数
     * @return string 返回date('Y-m-d H:i:s') 格式的日期字符串
     */
    public static function add_month($time_str, $add_month)
    {
        if ($add_month <= 0) {
            return date('Y-m-d H:i:s');
        }

        $arr = date_parse($time_str);
        $tmp = $arr['month'] + $add_month;
        $arr['month'] = $tmp > 12 ? ($tmp % 12) : $tmp;
        $arr['year'] = $tmp > 12 ? $arr['year'] + intval($tmp / 12) : $arr['year'];
        if ($arr['month'] == 0) {
            $arr['month'] = 12;
            $arr['year'] -= 1;
        }
        $max_days = $arr['month'] == 2 ? ($arr['year'] % 4 != 0 ? 28 : ($arr['year'] % 100 != 0 ? 29 : ($arr['year'] % 400 != 0 ? 28 : 29))) : (($arr['month'] - 1) % 7 % 2 != 0 ? 30 : 31);
        $arr['day'] = $arr['day'] > $max_days ? $max_days : $arr['day'];
        //fucking the Y2K38 bug
        $hour = !empty($arr['hour']) ? $arr['hour'] : 0;
        $minute = !empty($arr['minute']) ? $arr['minute'] : 0;
        $second = !empty($arr['second']) ? $arr['second'] : 0;
        return sprintf('%d-%02d-%02d %02d:%02d:%02d', $arr['year'], $arr['month'], $arr['day'], $hour, $minute, $second);
    }

    /**
     * 计算两个时间戳的差值
     * @param int $starttime 开始时间戳
     * @param int $endtime 结束时间错
     * @return array  时间差 ["day" => $days, "hour" => $hours, "min" => $mins, "sec" => $secs]
     */
    public static function diff_time($starttime, $endtime)
    {
        $timediff = $endtime - $starttime;
        $days = intval($timediff / 86400);
        $remain = $timediff % 86400;
        $hours = intval($remain / 3600);
        $remain = $remain % 3600;
        $mins = intval($remain / 60);
        $secs = $remain % 60;
        return ["day" => $days, "hour" => $hours, "min" => $mins, "sec" => $secs];
    }

    /**
     * 计算两个时间戳的差值 字符串
     * @param int $starttime 开始时间戳
     * @param int $endtime 结束时间错
     * @return string  时间差 xx小时xx分xx秒
     */
    public static function str_time($starttime, $endtime)
    {
        $c = abs(intval($endtime - $starttime));
        $s = $c % 60;
        $c = ($c - $s) / 60;
        $m = $c % 60;
        $h = ($c - $m) / 60;
        $rst = $h > 0 ? "{$h}小时" : '';
        $rst .= $m > 0 ? "{$m}分" : '';
        $rst .= "{$s}秒";
        return $rst;
    }

    /**
     * 把字节数 按照指定的单位显示
     * @param int $num 数值
     * @param string $in_tag 原单位 默认为空 表示 Byte
     * @param string $out_tag 需要的单位 默认为空 表示自动选取合适单位
     * @param int $dot_num 小数点位数 默认为2
     * @return string  修复后的字符串  自动加上单位
     */
    public static function byte_size($num, $in_tag = '', $out_tag = '', $dot_num = 2)
    {
        $num = $num * 1.0;
        $out_tag = strtoupper($out_tag);
        $in_tag = strtoupper($in_tag);
        $dot_num = $dot_num > 0 ? intval($dot_num) : 0;
        $tag_map = ['K' => 1024, 'M' => 1024 * 1024, 'G' => 1024 * 1024 * 1024, 'T' => 1024 * 1024 * 1024 * 1024];
        if (!empty($in_tag) && isset($tag_map[$in_tag])) {
            $num = $num * $tag_map[$in_tag];  //正确转换输入数据 去掉单位
        }
        $zero_list = [];
        for ($i = 0; $i < $dot_num; $i++) {
            $zero_list[] = '0';
        }
        $zero_str = '.' . join($zero_list, '');
        if ($num < 1024) {
            return str_replace($zero_str, '', sprintf("%.{$dot_num}f", $num));
        } else if (!empty($out_tag) && isset($tag_map[$out_tag])) {
            $tmp = round($num / $tag_map[$out_tag], $dot_num);
            return str_replace($zero_str, '', sprintf("%.{$dot_num}f", $tmp)) . $out_tag;  //使用设置的单位输出
        } else {
            foreach ($tag_map as $key => $val) {  //尝试找到一个合适的单位
                $tmp = round($num / $val, $dot_num);
                if ($tmp >= 1 && $tmp < 1024) {
                    return str_replace($zero_str, '', sprintf("%.{$dot_num}f", $tmp)) . $key;
                }
            }
            //未找到合适的单位  使用T进行输出
            return self::byte_size($num, '', 'T', $dot_num);
        }
    }


    public static function fix_telephone($telephone)
    {
        if (empty($telephone)) {
            return '';
        }
        $len = strlen($telephone);
        if ($len <= 7) {
            return '';
        }
        return substr($telephone, 0, 3) . str_repeat('*', $len - 7) . substr($telephone, -4);
    }

    public static function fix_email($email)
    {
        if (empty($email)) {
            return '';
        }
        $idx = strpos($email, '@');
        if ($idx <= 3) {
            return '';
        }
        return substr($email, 0, 3) . str_repeat('*', $idx - 3) . substr($email, $idx);
    }

    public static function str_cmp($str1, $str2)
    {
        if (!function_exists('hash_equals')) {
            if (strlen($str1) != strlen($str2)) {
                return false;
            } else {
                $res = $str1 ^ $str2;
                $ret = 0;
                for ($i = strlen($res) - 1; $i >= 0; $i--) {
                    $ret |= ord($res[$i]);
                }
                return !$ret;
            }
        } else {
            return hash_equals($str1, $str2);
        }
    }

    public static function str_icmp($str1, $str2)
    {
        return self::str_cmp(strtolower($str1), strtolower($str2));
    }

    public static function array_merge($arr1, $arr2, array $subkeys = [])
    {
        foreach ($subkeys as $subkey) {
            if (isset($arr1[$subkey])) {
                if (is_array($arr1[$subkey])) {
                    $arr2[$subkey] = isset($arr2[$subkey]) && is_array($arr2[$subkey]) ? $arr2[$subkey] : [];
                    $arr2[$subkey] = array_merge($arr1[$subkey], $arr2[$subkey]);
                } else {
                    $arr2[$subkey] = isset($arr2[$subkey]) ? $arr2[$subkey] : $arr1[$subkey];
                }
            }
        }
        return $arr2;
    }

    ##########################
    ######## 中文处理 ########
    ##########################

    /**
     * 计算utf8字符串长度
     * @param string $content 原字符串
     * @return int utf8字符串 长度
     */
    public static function utf8_strlen($content)
    {
        if (empty($content)) {
            return 0;
        }
        preg_match_all("/./us", $content, $match);
        return count($match[0]);
    }

    /**
     * 把utf8字符串中  gbk不支持的字符过滤掉
     * @param string $content 原字符串
     * @return string  过滤后的字符串
     */
    public static function utf8_gbk_able($content)
    {
        if (empty($content)) {
            return '';
        }
        $content = iconv("UTF-8", "GBK//TRANSLIT", $content);
        $content = iconv("GBK", "UTF-8", $content);
        return $content;
    }

    /**
     * 转换编码，将Unicode编码转换成可以浏览的utf-8编码
     * @param string $ustr 原字符串
     * @return string  转换后的字符串
     */
    public static function unicode_decode($ustr)  //
    {
        $pattern = '/(\\\u([\w]{4}))/i';
        preg_match_all($pattern, $ustr, $matches);
        $utf8_map = [];
        if (!empty($matches)) {
            foreach ($matches[0] as $uchr) {
                if (!isset($utf8_map[$uchr])) {
                    $utf8_map[$uchr] = self::unicode_decode_char($uchr);
                }
            }
        }
        $utf8_map['\/'] = '/';
        if (!empty($utf8_map)) {
            $ustr = str_replace(array_keys($utf8_map), array_values($utf8_map), $ustr);
        }
        return $ustr;
    }

    /**
     * 把 \uXXXX 格式编码的字符 转换为utf-8字符
     * @param string $uchar 原字符
     * @return string  转换后的字符
     */
    public static function unicode_decode_char($uchar)
    {
        $code = base_convert(substr($uchar, 2, 2), 16, 10);
        $code2 = base_convert(substr($uchar, 4), 16, 10);
        $char = chr($code) . chr($code2);
        $char = iconv('UCS-2', 'UTF-8', $char);
        return $char;
    }

    ##########################
    ######## 编码相关 ########
    ##########################

    /**
     * xss 清洗数组 尝试对数组中特定字段进行处理
     * @param array $data
     * @param array $keys
     * @return array 清洗后的数组
     */
    public static function xss_filter(array $data, array $keys)
    {
        foreach ($keys as $key) {
            if (!empty($data[$key]) && is_string($data[$key])) {
                $data[$key] = self::xss_clean($data[$key]);
            }
        }
        return $data;
    }

    /**
     * xss 过滤函数 清洗字符串
     * @param string $val
     * @return string
     */
    public static function xss_clean($val)
    {
        // this prevents some character re-spacing such as <java\0script>
        // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
        $val = preg_replace('/([\x00-\x09,\x0a-\x0c,\x0e-\x19])/', '', $val);
        $search = <<<EOT
abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$%^&*()~`";:?+/={}[]-_|'\<>
EOT;

        for ($i = 0; $i < strlen($search); $i++) {
            // @ @ search for the hex values
            $val = preg_replace('/(&#[xX]0{0,8}' . dechex(ord($search[$i])) . ';?)/i', $search[$i], $val); // with a ;
            // @ @ 0{0,7} matches '0' zero to seven times
            $val = preg_replace('/(&#0{0,8}' . ord($search[$i]) . ';?)/', $search[$i], $val); // with a ;
        }
        $val = preg_replace('/([<,>,",\'])/', '', $val);
        return $val;
    }

    /**
     * 安全的base64编码 替换'+/' 为 '-_' 自动消去末尾等号
     * @param string $str
     * @return string
     */
    public static function ub64_encode($str)
    {
        $str = rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
        return $str;
    }

    /**
     * 安全的base64解码
     * @param string $str
     * @return string
     */
    public static function ub64_decode($str)
    {
        $str = strtr(trim($str), '-_', '+/');
        $last_len = strlen($str) % 4;
        $str = $last_len == 2 ? $str . '==' : ($last_len == 3 ? $str . '=' : $str);
        $str = base64_decode($str);
        return $str;
    }

    /**
     * 加密字符串
     * @param string $crypt_key
     * @param string $string
     * @param int $expiry 有效期 秒数  默认为0表示永久有效
     * @return string
     */
    public static function encrypt($crypt_key, $string, $expiry = 0)
    {
        return authcode($string, 'ENCODE', $crypt_key, $expiry);
    }

    /**
     * 解密字符串
     * @param string $crypt_key
     * @param string $string
     * @return string
     */
    public static function decrypt($crypt_key, $string)
    {
        return authcode($string, 'DECODE', $crypt_key);
    }

    /**
     * 加解密操作
     * @param $string
     * @param $operation
     * @param $key
     * @param int $expiry
     * @return string
     */
    public static function authcode($string, $operation, $key, $expiry = 0)
    {
        if (empty($string)) {
            return '';
        }
        // 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
        $ckey_length = 4;
        // 密匙
        $key = md5($key);
        // 密匙a会参与加解密
        $keya = md5(substr($key, 0, 16));
        // 密匙b会用来做数据完整性验证
        $keyb = md5(substr($key, 16, 16));
        // 密匙c用于变化生成的密文
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) :
            substr(md5(microtime()), -$ckey_length)) : '';
        // 参与运算的密匙
        $cryptkey = $keya . md5($keya . $keyc);
        $key_length = strlen($cryptkey);
        // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，
        //解密时会通过这个密匙验证数据完整性
        // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
        $string = $operation == 'DECODE' ? safe_base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
        $string_length = strlen($string);
        $result = '';
        $box = range(0, 255);
        $rndkey = array();
        // 产生密匙簿
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }
        // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度
        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        // 核心加解密部分
        for ($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            // 从密匙簿得出密匙进行异或，再转成字符
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if ($operation == 'DECODE') {
            // 验证数据有效性，请看未加密明文的格式
            if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) &&
                substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)
            ) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            // 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
            // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
            return $keyc . str_replace('=', '', safe_base64_encode($result));
        }
    }


    /**
     * 拼接 url get 地址
     * @param string $base_url 基本url地址
     * @param array $args 附加参数
     * @return string  拼接出的网址
     */
    public static function build_url($base_url, array $args = [])
    {
        $base_url = stripos($base_url, '?') > 0 ? $base_url : "{$base_url}?";
        $base_url = (substr($base_url, -1) == '?' || substr($base_url, -1) == '&') ? $base_url : "{$base_url}&";
        $args_list = [];
        foreach ($args as $key => $val) {
            $key = trim($key);
            $args_list[] = "{$key}=" . urlencode($val);
        }
        return !empty($args_list) ? $base_url . join($args_list, '&') : $base_url;
    }

    /**
     * 获取一个数组的指定键值 未设置则使用 默认值
     * @param array $val
     * @param string $key
     * @param mixed $default 默认值 默认为 null
     * @return mixed
     */
    public static function v(array $val, $key, $default = null)
    {
        return isset($val[$key]) ? $val[$key] : $default;
    }

    /**
     * 根据魔术常量获取获取 类名 并转换为 小写字母加下划线格式 的 数据表名
     * @param string $str
     * @return string
     */
    public static function class2table($str)
    {
        $str = static::class2name($str);
        return strtolower(preg_replace('/((?<=[a-z])(?=[A-Z]))/', '_', $str));
    }

    /**
     * 根据魔术常量获取获取 类名
     * @param string $str
     * @return string
     */
    public static function class2name($str)
    {
        $idx = strripos($str, '::');
        $str = $idx > 0 ? substr($str, 0, $idx) : $str;
        $idx = strripos($str, '\\');
        $str = $idx > 0 ? substr($str, $idx + 1) : $str;
        return $str;
    }

    /**
     * 根据魔术常量获取获取 函数名
     * @param string $str
     * @return string
     */
    public static function method2name($str)
    {
        $idx = strripos($str, '::');
        $str = $idx > 0 ? substr($str, $idx + 2) : $str;
        return $str;
    }

    /**
     * 根据魔术常量获取获取 函数名 并转换为 小写字母加下划线格式 的 字段名
     * @param string $str
     * @return string
     */
    public static function method2field($str)
    {
        $str = static::method2name($str);
        return strtolower(preg_replace('/((?<=[a-z])(?=[A-Z]))/', '_', $str));
    }
}