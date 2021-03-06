<?php
/**
 * Ruby Rails風の処理を行う、あれこれ。
 *
 *
 * PHP versions 5
 *
 *
 * @category   MVC
 * @package    Envi3
 * @subpackage EnviMVCCore
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2013 Artisan Project
 * @license    http://opensource.org/licenses/BSD-2-Clause The BSD 2-Clause License
 * @version    GIT: $Id$
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        http://www.enviphp.net/
 * @since      File available since Release 1.0.0
 * @doc_ignore
 */

// 基本処理の記述
$start_time = microtime(true);
set_time_limit(0);
ini_set('memory_limit', -1);


define('ENVI_BASE_DIR', realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR);

// 引数の整形
foreach ($argv as $k => $item) {
    if (!strlen($item)) {
        unset($argv[$k]);
    }
}

ksort($argv);

$fargv = array_flip($argv);

/** 関数定義 */

/**
 * 引数の取得
 *
 * 指定した引数の次の値(-f "filename"のfilename)
 * を取得します。<br />
 * 存在しない場合は、
 * $default_paramの値を返す。
 *
 * @param string $name
 * @param mix $default_param (optional:false)
 */
function getOption($name, $default_param = false) {
    GLOBAL $argv,$fargv;
    if(isset($fargv[$name])){
        $x = $fargv[$name]+1;
        return isset($argv[$x]) ? $argv[$x] : false;
    } else {
        return $default_param;
    }
}

/**
 * $nameで指定された引数が存在するかどうかを確認する
 *
 * @param string $name
 */
function isOption($name) {
    GLOBAL $argv,$fargv;
    $fargv = array_flip($argv);
    return isset($fargv[$name]);
}

/**
 * 色付 echo
 */
function cecho($m, $c = 30, $oth = '') {
    if (DIRECTORY_SEPARATOR === '/') {
        system("echo -e '\e[{$c}m {$m} \e[m{$oth}'");
    } else {
        echo("{$m} {$oth}");
    }
}

/**
 * エラーメッセージ
 */
function eecho($err)
{
    fwrite(STDERR, "[ERR]".$err."\n");
}

// debug
$debug_mode = isOption("--debug");
function debug_msg($msg)
{
    global $debug_mode,$start_time;
    if ($debug_mode) {
        echo microtime(true) - $start_time." : {$msg}  --  ".memory_get_usage(true)."byte\r\n";
    }

}

require dirname(__FILE__).DIRECTORY_SEPARATOR.'help.php';

if (!isset($argv[1])) {
    die("error:propaty 1\n");
}

$task_dir = dirname(__FILE__).DIRECTORY_SEPARATOR;
if (strpos($argv[1], '-')) {
    list($module, $action) = explode('-', $argv[1], 2);
} else {
    require $task_dir.DIRECTORY_SEPARATOR.'alias.php';
    if (!isset($alias[$argv[1]])) {
        eecho('エイリアスの設定が無いです。');
        die;
    }
    list($module, $action) = $alias[$argv[1]];
}
$task_plugin_dir = $task_dir.'plugin'.DIRECTORY_SEPARATOR;

$task_file = $task_plugin_dir.$module.DIRECTORY_SEPARATOR.$action.'.php';
if (!is_file($task_file)) {
    eecho('タスクがありません');
    die;
}

require dirname(__FILE__).DIRECTORY_SEPARATOR.'libs'.DIRECTORY_SEPARATOR.'task.interface.php';
require $task_file;
