<?php
namespace BuiltinMock;

use phpmock\phpunit\PHPMock;
use PHPUnit_Framework_Assert;
use PHPUnit_Framework_TestCase;
use TinyWeb\Func;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/10 0010
 * Time: 10:25
 */
class FuncTest extends PHPUnit_Framework_TestCase
{

    use PHPMock;

    ##########################
    ######## 数组处理 ########
    ##########################

    public function test_filter_keys()
    {
        $test_i = [];
        $test_o = Func::filter_keys($test_i, ['a', 'b', 'c'], 1);
        $test_r = ['a' => 1, 'b' => 1, 'c' => 1];
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);

        $test_i = ['a' => 2, 'b' => 2];
        $test_o = Func::filter_keys($test_i, ['a', 'b', 'c'], 1);
        $test_r = ['a' => 2, 'b' => 2, 'c' => 1];
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);

        $test_i = ['a' => 2, 'b' => 2];
        $test_o = Func::filter_keys($test_i, [], 1);
        $test_r = [];
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);

        $test_i = ['a' => 2, 'b' => 2];
        $test_o = Func::filter_keys($test_i, ['a', 'b', 'c']);
        $test_r = ['a' => 2, 'b' => 2, 'c' => ''];
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);

        $test_i = [];
        $test_o = Func::filter_keys($test_i, []);
        $test_r = [];
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);
    }

    public function test_v()
    {
        $test_i = ['a' => 2, 'b' => 2];
        $test_o = Func::v($test_i, 'c');
        $test_r = null;
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);

        $test_i = ['a' => 2, 'b' => 2];
        $test_o = Func::v($test_i, 'b');
        $test_r = 2;
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);

        $test_i = ['a' => 2, 'b' => 2];
        $test_o = Func::v($test_i, 'c', 3);
        $test_r = 3;
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);
    }

    ##########################
    ######## 时间处理 ########
    ##########################

    public function test_add_month()
    {
        $test_i = '2017-01-21 12:34:56';
        $test_o = Func::add_month($test_i, 1);
        $test_r = '2017-02-21 12:34:56';
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);

        $test_i = '2017-01-31 12:34:56';
        $test_o = Func::add_month($test_i, 1);
        $test_r = '2017-02-28 12:34:56';
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);

        $test_i = '2016-01-31 12:34:56';
        $test_o = Func::add_month($test_i, 1);
        $test_r = '2016-02-29 12:34:56';
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);

        $test_i = '2016-02-29 12:34:56';
        $test_o = Func::add_month($test_i, 12);
        $test_r = '2017-02-28 12:34:56';
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);

        $test_i = '2016-02-29 12:34:56';
        $test_o = Func::add_month($test_i, 0);
        $test_r = '2016-02-29 12:34:56';
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);

        $test_i = '2016-02-29 12:34:56';
        $test_o = Func::add_month($test_i, -1);
        $test_r = '2016-02-29 12:34:56';
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);

        $test_i = '2016-02-29 12:34:56';
        $test_o = Func::add_month($test_i, 48000);
        $test_r = '6016-02-29 12:34:56';
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);
    }

    public function test_diff_time()
    {
        $test_i = time();
        $test_o = Func::diff_time($test_i, $test_i + 60 + 1);
        $test_r = ["day" => 0, "hour" => 0, "min" => 1, "sec" => 1];
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);

        $test_i = time();
        $test_o = Func::diff_time($test_i, $test_i + 3600 + 60 + 1);
        $test_r = ["day" => 0, "hour" => 1, "min" => 1, "sec" => 1];
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);

        $test_i = time();
        $test_o = Func::diff_time($test_i, $test_i + 24 * 3600 + 3600 + 60 + 1);
        $test_r = ["day" => 1, "hour" => 1, "min" => 1, "sec" => 1];
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);

        for ($i = 0; $i < 100; $i++) {
            list($day, $hour, $min, $sec) = [rand(0, 10000), rand(0, 23), rand(0, 59), rand(0, 59)];
            $test_i = time();
            $tmp = $test_i + $day * 24 * 3600 + $hour * 3600 + $min * 60 + $sec * 1;
            $test_o = Func::diff_time($test_i, $tmp);
            $test_o2 = Func::diff_time($tmp, $test_i);
            $test_r = ["day" => $day, "hour" => $hour, "min" => $min, "sec" => $sec];
            PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);
            PHPUnit_Framework_Assert::assertEquals($test_r, $test_o2);
        }
    }

    public function test_str_time()
    {
        $test_i = time();
        $test_o = Func::str_time($test_i, $test_i + 60 + 1);
        $test_r = "1分1秒";
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);

        $test_i = time();
        $test_o = Func::str_time($test_i, $test_i + 3600 + 60 + 1);
        $test_r = "1小时1分1秒";
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);

        $test_i = time();
        $test_o = Func::str_time($test_i, $test_i + 24 * 3600 + 3600 + 60 + 1);
        $test_r = "25小时1分1秒";
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);

        for ($i = 0; $i < 100; $i++) {
            list($hour, $min, $sec) = [rand(0, 23), rand(0, 59), rand(0, 59)];
            $test_i = time();
            $tmp = $test_i + $hour * 3600 + $min * 60 + $sec * 1;
            $test_o = Func::str_time($test_i, $tmp);
            $test_o2 = Func::str_time($tmp, $test_i);
            $test_r = $hour > 0 ? "{$hour}小时" : '';
            $test_r .= $min > 0 ? "{$min}分" : '';
            $test_r .= $sec > 0 ? "{$sec}秒" : '';
            PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);
            PHPUnit_Framework_Assert::assertEquals($test_r, $test_o2);
        }
    }

    ##########################
    ######## 字符串处理 ########
    ##########################

    public function test_pass_filter()
    {
        $test_i = 'abc,def,abcd';
        $test_o = Func::pass_filter($test_i, 'abc');
        PHPUnit_Framework_Assert::assertFalse($test_o);

        $test_i = 'abc,def,abcd';
        $test_o = Func::pass_filter($test_i, 'aaa|def');
        PHPUnit_Framework_Assert::assertFalse($test_o);

        $test_i = 'abc,def,abcd';
        $test_o = Func::pass_filter($test_i, 'aaa,def,', ',');
        PHPUnit_Framework_Assert::assertFalse($test_o);

        $test_i = 'abc,def,abcd';
        $test_o = Func::pass_filter($test_i, 'abcde');
        PHPUnit_Framework_Assert::assertTrue($test_o);

        $test_i = 'abc,def,abcd';
        $test_o = Func::pass_filter($test_i, 'abcde  |  | defa');
        PHPUnit_Framework_Assert::assertTrue($test_o);
    }

    public function test_byte2size()
    {
        $test_i = 1024;
        $test_o = Func::byte2size($test_i);
        $test_r = '1K';
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);

        $test_i = 1024 + 512;
        $test_o = Func::byte2size($test_i, 'K');
        $test_r = '1.50M';
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);

        $test_i = 1024 + 512;
        $test_o = Func::byte2size($test_i, 'K', '', 3);
        $test_r = '1.500M';
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);

        $test_i = (1024 + 512) * 10;
        $test_o = Func::byte2size($test_i, 'K', 'G', 3);
        $test_r = '0.015G';
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);

        $test_i = 0.0125;
        $test_o = Func::byte2size($test_i, 'G');
        $test_r = '12.80M';
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);
    }

    public function test_anonymous_telephone()
    {
        $test_i = '15099991234';
        $test_o = Func::anonymous_telephone($test_i);
        $test_r = '150****1234';
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);

        $test_i = '150999912345678';
        $test_o = Func::anonymous_telephone($test_i);
        $test_r = '150********5678';
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);

        $test_i = '15099991234';
        $test_o = Func::anonymous_telephone($test_i, 2, 3);
        $test_r = '15******234';
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);

        $test_i = '1509999';
        $test_o = Func::anonymous_telephone($test_i);
        $test_r = '1509999';
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);
    }

    public function test_anonymous_email()
    {
        $test_i = 'one20170101@test.com';
        $test_o = Func::anonymous_email($test_i);
        $test_r = 'one********@test.com';
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);

        $test_i = 'one20170101@test.com';
        $test_o = Func::anonymous_email($test_i, 4);
        $test_r = 'one2*******@test.com';
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);

        $test_i = 'one@test.com';
        $test_o = Func::anonymous_email($test_i);
        $test_r = 'one@test.com';
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);

        $test_i = 'one20170101#test.com';
        $test_o = Func::anonymous_email($test_i);
        $test_r = 'one20170101#test.com';
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);
    }

    public function test_str_cmp()
    {
        $test_i_1 = 'abc';
        $test_i_2 = 'abc';
        $test_o = Func::str_cmp($test_i_1, $test_i_2);
        PHPUnit_Framework_Assert::assertTrue($test_o);

        $test_i_1 = '20170101';
        $test_i_2 = 20170101;
        $test_o = Func::str_cmp($test_i_1, $test_i_2);
        PHPUnit_Framework_Assert::assertTrue($test_o);

        $test_i_1 = 'abc';
        $test_i_2 = 'abc ';
        $test_o = Func::str_cmp($test_i_1, $test_i_2);
        PHPUnit_Framework_Assert::assertFalse($test_o);

        $test_i_1 = 'abc';
        $test_i_2 = ' abc';
        $test_o = Func::str_cmp($test_i_1, $test_i_2);
        PHPUnit_Framework_Assert::assertFalse($test_o);
    }

    // PHP中 == 运算符的安全问题
    // http://blog.jobbole.com/104107/
    protected static $safe_str_data = [
        ["0xff", "255"],
        ["1.00000000000000001", "0.1e1"],
        ["+1", "0.1e1"],
        ["1e0", "0.1e1"],
        ["-0e10", "0"],
        ["1000", "0x3e8"],
        ["1234", "  	1234"],
    ];

    public function test_str_cmp2()
    {
        foreach (self::$safe_str_data as $item) {
            list($test_i_1, $test_i_2) = $item;
            PHPUnit_Framework_Assert::assertTrue($test_i_1 == $test_i_2);
            $test_o = Func::str_cmp($test_i_1, $test_i_2);
            PHPUnit_Framework_Assert::assertFalse($test_o);
        }

        list($test_i_1, $test_i_2) = [md5('c!C123449477'), md5('d!D206687225')];
        PHPUnit_Framework_Assert::assertTrue($test_i_1 == $test_i_2);
        $test_o = Func::str_cmp($test_i_1, $test_i_2);
        PHPUnit_Framework_Assert::assertFalse($test_o);

        list($test_i_1, $test_i_2) = [md5('e!E160399390'), md5('f!F24413812')];
        PHPUnit_Framework_Assert::assertTrue($test_i_1 == $test_i_2);
        $test_o = Func::str_cmp($test_i_1, $test_i_2);
        PHPUnit_Framework_Assert::assertFalse($test_o);

        list($test_i_1, $test_i_2) = [sha1('aA1537368460!'), sha1('fF3560631665!')];
        PHPUnit_Framework_Assert::assertTrue($test_i_1 == $test_i_2);
        $test_o = Func::str_cmp($test_i_1, $test_i_2);
        PHPUnit_Framework_Assert::assertFalse($test_o);
    }

    public function test_stri_cmp()
    {
        $test_i_1 = 'abc';
        $test_i_2 = 'ABC';
        $test_o = Func::stri_cmp($test_i_1, $test_i_2);
        PHPUnit_Framework_Assert::assertTrue($test_o);

        $test_i_1 = 'abc';
        $test_i_2 = 'ABc';
        $test_o = Func::stri_cmp($test_i_1, $test_i_2);
        PHPUnit_Framework_Assert::assertTrue($test_o);

        $test_i_1 = '20170101';
        $test_i_2 = 20170101;
        $test_o = Func::stri_cmp($test_i_1, $test_i_2);
        PHPUnit_Framework_Assert::assertTrue($test_o);

        $test_i_1 = 'abc';
        $test_i_2 = 'Abc ';
        $test_o = Func::stri_cmp($test_i_1, $test_i_2);
        PHPUnit_Framework_Assert::assertFalse($test_o);
    }

    public function test_utf8_strlen()
    {
        $test_i = 'utf8长度';
        $test_o = Func::utf8_strlen($test_i);
        $test_r = 6;
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);

        $test_i = '长度';
        $test_o = Func::utf8_strlen($test_i);
        $test_r = 2;
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);

        $test_i = 'utf8';
        $test_o = Func::utf8_strlen($test_i);
        $test_r = 4;
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);

        $test_i = '';
        $test_o = Func::utf8_strlen($test_i);
        $test_r = 0;
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);

        $test_i = ' ';
        $test_o = Func::utf8_strlen($test_i);
        $test_r = 1;
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);
    }

    public function test_utf8_gbk_able()
    {
        $test_i = '你我他';
        $test_o = Func::utf8_gbk_able($test_i);
        $test_r = '你我他';
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);

        $test_i = 'abc';
        $test_o = Func::utf8_gbk_able($test_i);
        $test_r = 'abc';
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);
    }

    public function test_unicode_decode()
    {
        $test_i = '\u89e3\u51bb\u8d26\u53f7\u6210\u529f';
        $test_o = Func::unicode_decode($test_i);
        $test_r = '解冻账号成功';
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);
    }

    public function test_unicode_decode_char()
    {
        $test_i = '\u89e3';
        $test_o = Func::unicode_decode_char($test_i);
        $test_r = '解';
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);
    }

    ##########################
    ######## 编码相关 ########
    ##########################

    protected static $test_str_data = [
        '',
        'abcd',
        '    ',
        1234,
        123456789,
        12345.6789,
        '123456789',
        'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
    ];

    public function test_safe_base64()
    {
        foreach (self::$test_str_data as $test_i) {
            $rst = Func::safe_base64_encode($test_i);
            $test_o = Func::safe_base64_decode($rst);
            PHPUnit_Framework_Assert::assertEquals($test_i, $test_o);
        }

        foreach (self::$test_str_data as $test_i) {
            $test_i = strval($test_i);
            $rst = Func::safe_base64_encode($test_i);
            $test_o = Func::safe_base64_decode($rst);
            PHPUnit_Framework_Assert::assertEquals($test_i, $test_o);
        }
    }

    public function test_authcode_expiry4()
    {
        $key = 'zT5hF$E24*(#dfS^Yq3&6A^6';
        $test_i = 'abc';
        $now = time();
        $rst = Func::encode($test_i, $key, 10 * 365 * 24 * 3600);   //设置有效期为 10 年

        $time = $this->getFunctionMock(Func::_namespace(), "time");  // Mock Func 命名空间下 time 函数
        $time->expects($this->once())->willReturn($now + 10 * 365 * 24 * 3600 - 10);    //模拟 10年 - 10秒后的时间

        $test_o = Func::decode($rst, $key);
        PHPUnit_Framework_Assert::assertEquals($test_i, $test_o);
    }

    public function test_authcode_expiry3()
    {
        $key = 'zT5hF$E24*(#dfS^Yq3&6A^6';
        $test_i = 'abc';
        $now = time();
        $rst = Func::encode($test_i, $key, 10 * 365 * 24 * 3600);   //设置有效期为 10 年

        $time = $this->getFunctionMock(Func::_namespace(), "time");  // Mock Func 命名空间下 time 函数
        $time->expects($this->once())->willReturn($now + 10 * 365 * 24 * 3600 + 10);    //模拟 10年 + 10秒后的时间

        $test_o = Func::decode($rst, $key);
        PHPUnit_Framework_Assert::assertEmpty($test_o);
    }

    public function test_authcode_expiry2()
    {
        $key = 'zT5hF$E24*(#dfS^Yq3&6A^6';
        $test_i = 'abc';
        $now = time();
        $rst = Func::encode($test_i, $key, 10);   //设置有效期为 10 s

        $time = $this->getFunctionMock(Func::_namespace(), "time");  // Mock Func 命名空间下 time 函数
        $time->expects($this->once())->willReturn($now + 20);    //模拟 20秒后的时间

        $test_o = Func::decode($rst, $key);
        PHPUnit_Framework_Assert::assertEmpty($test_o);
    }

    public function test_authcode_expiry1()
    {
        $key = 'zT5hF$E24*(#dfS^Yq3&6A^6';
        $test_i = 'abc';
        $rst = Func::encode($test_i, $key, 10);

        $test_o = Func::decode($rst, $key);
        PHPUnit_Framework_Assert::assertEquals($test_i, $test_o);
    }

    public function test_authcode()
    {
        $key = 'zT5hF$E24*(#dfS^Yq3&6A^6';
        foreach (self::$test_str_data as $test_i) {
            $rst = Func::encode($test_i, $key);
            $test_o = Func::decode($rst, $key);
            PHPUnit_Framework_Assert::assertEquals($test_i, $test_o);
        }

        $key = 'zT5hF$E24*(#dfS^Yq3&6A^6';
        foreach (self::$test_str_data as $test_i) {
            $test_i = strval($test_i);
            $rst = Func::encode($test_i, $key);
            $test_o = Func::decode($rst, $key);
            PHPUnit_Framework_Assert::assertEquals($test_i, $test_o);
        }

        $key1 = 'zT5hF$E24*(#dfS^Yq3&6A^6';
        $key2 = 'zT5hF$E24*(#dfS^Yq3&6A^5';
        foreach (self::$test_str_data as $test_i) {
            $rst = Func::encode($test_i, $key1);
            $test_o = Func::decode($rst, $key2);
            PHPUnit_Framework_Assert::assertEmpty($test_o);
        }
    }

    public function test_xss_filter()
    {
        $test_i = ['abc' => "<div >bcd\n</div>"];
        $test_o = Func::xss_filter($test_i, ['abc']);
        $test_r = ['abc' => "div bcd/div"];
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);
    }

    public function test_xss_clean()
    {
        $test_i = "<div >bcd\n</div>\t";
        $test_o = Func::xss_clean($test_i);
        $test_r = "div bcd/div";
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);
    }

    ##########################
    ######## URL相关 ########
    ##########################

    public function test_build_url()
    {
        $test_i_1 = "http://test.com";
        $test_o = Func::build_url($test_i_1);
        $test_r = "http://test.com";
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);

        $test_i_1 = "http://test.com";
        $test_i_2 = ['a' => 1, 'b' => 2];
        $test_o = Func::build_url($test_i_1, $test_i_2);
        $test_r = "http://test.com/?a=1&b=2";
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);

        $test_i_1 = "http://test.com/";
        $test_i_2 = ['a' => 1, 'b' => 2];
        $test_o = Func::build_url($test_i_1, $test_i_2);
        $test_r = "http://test.com/?a=1&b=2";
        PHPUnit_Framework_Assert::assertEquals($test_r, $test_o);
    }
}