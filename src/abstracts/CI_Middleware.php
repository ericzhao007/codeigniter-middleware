<?php
namespace Codeigniter\Middleware\Abstracts;

/**
 * 中间件基类
 *
 * @Author EricZhao 
 * @DateTime 2021-08-26 17:20
 */
abstract class CI_Middleware
{
    /**
     * Constructor
     *
     * @access public
     */
    function __construct()
    {
        log_message('debug', "middleware Class Initialized");
    }

    abstract public function handle(\Closure $next);

    /**
     * __get
     *
     * Allows models to access CI's loaded classes using the same
     * syntax as controllers.
     *
     * @param	string
     * @access private
     */
    function __get($key)
    {
        $CI =& get_instance();
        return $CI->$key;
    }
}
