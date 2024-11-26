<?php

use alone\expand\Safe;
use alone\toolkit\Frame;
use alone\expand\Except;
use alone\toolkit\CliClassHerper;
use alone\toolkit\CacheHelper;

if (!function_exists('ps')) {
    function ps(mixed $data, bool $type = true) {
        $content = '<pre>' . print_r($data, true) . '</pre>';
        if (empty($type)) {
            return $content;
        }
        echo $content;
        return '';
    }
}

if (!function_exists('ts')) {
    function ts(mixed $data = null, int $row = 30, int $cols = 200) {
        echo '<textarea rows="' . $row . '" cols="' . $cols . '">' . $data . '</textarea>';
    }
}
if (!function_exists('es')) {
    function es(mixed $data = null, int $row = 30, int $cols = 200) {
        return '<textarea rows="' . $row . '" cols="' . $cols . '">' . $data . '</textarea>';
    }
}

/**
 * 生成12/13位时间
 * @param null|string|int|float $time
 * @return string
 */

if (!function_exists('get_time')) {
    function get_time(null|string|int|float $time = null): string {
        list($t1, $t2) = explode(" ", (!empty($time) ? $time : microtime()));
        $timeArr = explode(".", $t2 . ($t1 * 1000));
        return $timeArr[key($timeArr)];
    }
}

/**
 * 生成13位时间
 * @param string|int|float|null $time
 * @return float
 */

if (!function_exists('get_unix')) {
    function get_unix(null|string|int|float $time = null): float {
        list($t1, $t2) = explode(" ", (!empty($time) ? $time : microtime()));
        return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
    }
}

/**
 * 13位时间转10位
 * @param $time
 * @return float|int|string
 */
if (!function_exists('unix_time')) {
    function unix_time($time): float|int|string {
        return (int)(ceil($time / 1000));
    }
}

/**
 * 13位转时间
 * @param string|int $time
 * @param string $format
 * @return string
 */
if (!function_exists('time_date')) {
    function time_date($time, string $format = 'Y-m-d H:i:s'): string {
        return get_date(unix_time($time), $format);
    }
}
/**
 * 获取当前时间
 * @param string|int $time
 * @param string $format
 * @return string
 */
if (!function_exists('get_date')) {
    function get_date(string|int $time = '', string $format = 'Y-m-d H:i:s'): string {
        return date($format, (!empty($time) ? $time : time()));
    }
}
/**
 * @param $data
 * @return string
 */
if (!function_exists('pack_num')) {
    function pack_num($data) {
        return \bin2hex(\pack('N', $data));
    }
}

/**
 * 判断是否Cli
 */
if (!function_exists('isCli')) {
    function isCli(): bool|int {
        return preg_match("/cli/i", php_sapi_name());
    }
}

if (!function_exists('cliColor')) {
    function cliColor($data, int $type = 1): string {
        $req = function_exists('request');
        return ((isCli() && (!empty($req) && empty(request()))) ? ("\033[38;5;" . $type . ";1m" . $data . "\033[0m") : ($data));
    }
}

if (!function_exists('mov_en')) {
    /**
     * 加密
     * @param array $arr
     * @param array $mode
     * @return array
     */
    function mov_en(array $arr, array $mode = ['aes', 'des', 'des3']): array {
        return Safe::movEn($arr, $mode);
    }
}

if (!function_exists('mov_de')) {
    /**
     * 解密
     * @param array $arr
     * @param array $array
     * @return mixed
     */
    function mov_de(array $arr, array $array = []): mixed {
        try {
            if (!empty($data = Frame::getArr($arr, 'data')) && !empty($random = Frame::getArr($arr, 'random'))) {
                if (!empty($json = Safe::movDe($data, $random))) {
                    if (!empty($array = Frame::isJson($json))) {
                        return $array;
                    }
                }
            }
            return $array;
        } catch (\Throwable $e) {
            return $array;
        }
    }
}

if (!function_exists('en_pass')) {
    /**
     * 加密密码
     * @param string $pass
     * @return string
     */
    function en_pass(string $pass): string {
        return password_hash($pass, PASSWORD_DEFAULT);
    }
}
if (!function_exists('verify_pass')) {
    /**
     * 密码验证
     * @param string $pass
     * @param string $hash
     * @return bool
     */
    function verify_pass(string $pass, string $hash): bool {
        return password_verify($pass, $hash);
    }
}

if (!function_exists('arrToObj')) {
    /**
     * @param array $array 要转换的array
     * @param bool $type 是否支持多级
     * @return stdClass
     */
    function toObj(array $array, bool $type = true): stdClass {
        $obj = new stdClass();
        foreach ($array as $key => $value) {
            if (is_array($value) && !empty($type)) {
                $obj->$key = toObj($value);
            } else {
                $obj->$key = $value;
            }
        }
        return $obj;
    }
}

if (!function_exists('get_order_id')) {
    /**
     * 生成订单号
     * @param string $token 自定标识,为空随机生成token
     * @param int $unix 顺序,为空按当前时间13位
     * @param string $type md5-16 md5-32 sha-256 crc-32
     * @return string
     */
    function get_order_id(string $token = '', int $unix = 0, string $type = 'md5-32'): string {
        return Frame::getOrderId($token, $unix, $type);
    }
}

if (!function_exists('save_xls')) {
    /**
     * 数据保存表格,支持 Xls Xlsx Csv 三种
     * @param array $row 设置字段和表头名称 格式: ['user'=>'会员帐号','time'=>'注册时间']
     * @param array $array 表格数据,二维array 按row设置的字段设置数据  格式: [['user'=>'admin','time'=>'2023-01-01']]
     * @param string $file 保存位置带格式,绝对路径
     * @return bool
     */
    function save_xls(array $row, array $array, string $file): bool {
        return Except::save($row, $array, $file);
    }
}

if (!function_exists('throwNew')) {
    function throwNew(string $message = "", int $code = 0, Throwable|null $previous = null) {
        throw new \Exception($message, $code, $previous);
    }
}

if (!function_exists('getClassInfo')) {
    /**
     * 获取类的信息
     * @param string $class 类名,有namespace要带上
     * @param string|null $key attr=属性值列表,method=公开方法列表,attribute=属性列表信息,magic=魔术方法列表,
     * @param mixed $def
     * @return mixed
     */
    function getClassInfo(string $class, string|null $key = null, mixed $def = ''): mixed {
        return CliClassHerper::get($class, $key, $def);
    }
}

if (!function_exists('getClassDoc')) {
    /**
     * 类使用执行
     * @param string $class 类名,有namespace要带上
     * @param array|bool $config new参数
     * @param bool $def 是否提示默认值
     * @return array
     */
    function classExec(string $class, array|bool $config = [], bool $def = false): array {
        return CliClassHerper::exec($class, $config, $def);
    }
}

if (!function_exists('classCli')) {
    /**
     * 类使用cli
     * @param string $class 类名,有namespace要带上
     * @param int $row cli 左则位数
     * @param int $len cli 总长度
     * @param array|bool $config new参数
     * @param bool $def 是否提示默认值
     * @return void
     */
    function classCli(string $class, int $row = 21, int $len = 90, array|bool $config = [], bool $def = false): void {
        CliClassHerper::cli($class, $row, $len, $config, $def);
    }
}

if (!function_exists('exec_hits')) {
    /**
     * 执行指定包时,不是想要的返回内容时会再次执行
     * @param string|int $name 名称类型
     * @param int $hits 执行次数
     * @param callable $callable 执行包(hits)
     * @param mixed $decide array时设置判断条件[key,val]不相等时再次执行,callable($res,$hits)=返回true时执行,其他类型时设置判断相等时再次执行
     * ['code',200] 执行包返回 array['code']!=200 再次执行
     * callable($res,$hits) 执行包返回true时再次执行
     * 其他类型      执行包返回 相等时 再次执行
     * @return mixed
     */
    function exec_hits(string|int $name, int $hits, callable $callable, mixed $decide = ['code', 200]): mixed {
        // 获取执行次数据,为0时执行成功
        // CacheHelper::cacheGet('exec_hits', $name, 0);
        return CacheHelper::execHits($name, $hits, $callable, $decide);
    }
}
if (!function_exists('redis_lock')) {
    /**
     * redis 排他锁 执行
     * @param string|int $key 唯一标识
     * @param callable $callable 执行包
     * @param callable|bool $closure 超时的时候处理,false=不处理,true=运行执行包,callable($callable)=自定执行包
     * @param int $timeout 有效时间,有同时执行的最长等待时间 秒
     * @param int $wait 间隔等待时间 微秒
     * @return mixed
     */
    function redis_lock(string|int $key, callable $callable, callable|bool $closure = false, int $timeout = 5, int $wait = 100): mixed {
        return CacheHelper::redisLock($key, $callable, $closure, $timeout, $wait);
    }
}
if (!function_exists('file_lock')) {
    /**
     * 文件 排他锁 执行
     * @param string|int $key 唯一标识 和 file_lock_exec 同用
     * @param callable $callable 执行包
     * @param callable|bool $closure 超时的时候处理,false=不处理,true=运行执行包,callable($callable)=自定执行包
     * @param int $timeout 有效时间,有同时执行的最长等待时间 秒
     * @param int $wait 间隔等待时间 微秒
     * @return mixed
     */
    function file_lock(string|int $key, callable $callable, callable|bool $closure = false, int $timeout = 5, int $wait = 100): mixed {
        return CacheHelper::fileLock($key, $callable, $closure, $timeout, $wait);
    }
}
if (!function_exists('file_lock_exec')) {
    /**
     * 文件 独占锁 执行 会一直阻塞 直到完成
     * @param string|int $key 唯一标识 和 file_lock 同用
     * @param callable $callable 执行包
     * @return mixed
     */
    function file_lock_exec(string|int $key, callable $callable): mixed {
        return CacheHelper::fileLockExec($key, $callable);
    }
}

if (!function_exists('error_html')) {
    /**
     * 报错html
     * @param string|int $title
     * @param string|int $content
     * @return mixed
     */
    function error_html(string|int $title = '', string|int $content = ''): mixed {
        return Frame::textTag((@file_get_contents(run_path('/alone/html/error.html'))), ['title' => ($title ?: '400'), 'content' => ($content ?: 'error')]);
    }
}

if (!function_exists('random_float')) {
    /**
     * 随机小数
     * @param int|float $min 最小
     * @param int|float $max 最大
     * @param bool $type 是否支持负数
     * @return int|float
     */
    function random_float(int|float $min = 0.01, int|float $max = 0.2, bool $type = false): int|float {
        $min = abs($min);
        $max = abs($max);
        $num = $min + mt_rand() / mt_getrandmax() * ($max - $min);
        $num = sprintf("%.2f", $num);
        return ((rand(1, 2) == 1 && $type) ? (-$num) : $num);
    }
}

if (!function_exists('random_balance_float')) {
    /**
     * 随机金额
     * @param int|float $balance 订单金额
     * @param int|float $min 最小
     * @param int|float $max 最大
     * @param array $arr 现有金额列表
     * @param int $i
     * @return int|float
     */
    function random_balance_float(int|float $balance, int|float $min, int|float $max, array $arr = [], int $i = 0): int|float {
        $money = Frame::money(($balance - random_float($min, $max)));
        if (!empty($arr)) {
            if (in_array($money, $arr)) {
                if ($i >= 10) {
                    return 0;
                }
                return random_balance_float($balance, $min, $max, $arr, (++$i));
            }
        }
        return $money;
    }
}