<?php
/**
 * DB����
 *
 * PHP versions 5
 *
 *
 * @category   MVC
 * @package    Envi3
 * @subpackage EnviMVCCore
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2012 Artisan Project
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    GIT: $Id$
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        https://github.com/EnviMVC/EnviMVC3PHP/wiki
 * @since      File available since Release 1.0.0
 */
/**
 * pearDB���̃C���X�^���X��񋟂��邽�߂̃��b�p�[
 *
 * @package    Envi3
 * @subpackage EnviMVCCore
 * @author     Akito <akito-artisan@five-foxes.com>
 * @copyright  2011-2012 Artisan Project
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: @package_version@
 * @link       https://github.com/EnviMVC/EnviMVC3PHP
 * @see        https://github.com/EnviMVC/EnviMVC3PHP/wiki
 * @since      Class available since Release 1.0.0
 */
class DB
{
    const AUTOQUERY_INSERT = 1;
    const AUTOQUERY_UPDATE = 2;
    const AUTOQUERY_REPLACE = 3;

    private static $connections = array();


    /**
     * +-- Envi����Ă΂�郁�\�b�h�B�K�����
     *
     * @access public
     * @static
     * @param  $param
     * @param  $instance_name
     * @return EnviDBIBase
     */
    public static function getConnection($param, $instance_name)
    {
        if (isset(self::$connections[$instance_name])) {
            return self::$connections[$instance_name];
        }
        if (is_array($param['dsn'])) {
            shuffle($param['dsn']);
            foreach ($param['dsn'] as $dsn) {
                $dbi = self::getNewConnection($dsn, $param);
                if ($dbi !== false) {
                    break;
                }
            }
        } else {
            $dbi = self::getNewConnection($param['dsn'], $param);
        }
        
        if ($param['instance_pool']) {
            self::$connections[$instance_name] = $dbi;
        }
        return $dbi;
    }
    /* ----------------------------------------- */


    private static function getNewConnection($dsn, array $param)
    {
        parse_str($dsn, $conf);
        try{
            $dbi = self::connect($conf, '', '', $param['connection_pool']);
            if ($param['initialize_query']) {
                $dbi->query($param['initialize_query']);
            }
            return $dbi;
        } catch (exception $e) {
            return false;
        }
    }

    /**
     * +-- EnviDBIBase���擾����
     *
     * @access public
     * @static
     * @param  $dsn
     * @param  $user OPTIONAL:false
     * @param  $password OPTIONAL:false
     * @return EnviDBIBase
     */
    public static function connect($dsn, $user = false, $password = false, $is_pool = false)
    {
        if ($is_pool) {
            if ($user === false) {
                return new EnviDBIBase($dsn, '', '',  array(PDO::ATTR_PERSISTENT => true));
            } else {
                return new EnviDBIBase($dsn, $user, $password, array(PDO::ATTR_PERSISTENT => true));
            }
        } else {
            if ($user === false) {
                return new EnviDBIBase($dsn, '', '');
            } else {
                return new EnviDBIBase($dsn, $user, $password);
            }
        }
    }
    /* ----------------------------------------- */

    /**
     * +-- �_�~�[
     *
     * @static
     * @param & $obj
     * @return boolean
     */
    public static function isError(&$obj)
    {
        return false;
    }
    /* ----------------------------------------- */
}