<?php
namespace Codeigniter\Middleware;
/**
 * 扩展CI增加中间件功能
 *
 * @Author EricZhao <1091588684@qq.com>
 * @DateTime 2021-08-26 16:38
 */
class Middleware
{
    private $controller;
    public function __construct($controller)
    {
        $this->controller = $controller;
    }

    /**
     * 启动
     *
     * @param string $name method
     * @param array $arguments url path
     * @Author EricZhao <1091588684@qq.com>
     * @DateTime 2021-08-26 16:36
     */
    public function start($name, $arguments)
    {
        if (is_callable([$this->controller, $name])) {
            // 获取中间件
            $middles = $this->getMiddlewares($name);
            
            // 开始执行
            $reponse = $this->invokeMiddlewares($middles, function() use ($name, $arguments){
                return call_user_func_array([$this->controller, $name], $arguments);
            });

            // 结果输出
            $this->output($reponse);
        }else{
            $class = get_class($this->controller);
            show_404("{$class}/{$name}");
        }
    }

    /**
     * 输出
     *
     * @param mixed $reponse
     * @Author EricZhao <1091588684@qq.com>
     * @DateTime 2021-08-26 18:04
     */
    public function output($reponse){
        if(!empty($reponse) && is_array($reponse)){
            echo json_encode($reponse);
        }else{
            echo $reponse;
        }
    }

    /**
     * 获取可执行中间件
     *
     * @param string $method 控制器方法
     * @Author EricZhao <1091588684@qq.com>
     * @DateTime 2021-08-25 11:47
     */
    public function getMiddlewares($method)
    {
        $middlewares = [];
        if(is_callable([$this->controller, 'getMiddlewares'])){
            // 从控制器取到注册的中间件
            $middles = call_user_func([$this->controller, 'getMiddlewares']);
            foreach($middles as $middle){
                if(!empty($middle['except']) && in_array($method, $middle['except'])){  // 黑名单
                    continue;
                }
                if(!empty($middle['only']) && !in_array($method, $middle['only'])){    // 白名单
                    continue;
                }
                $middlewares[] = $middle;
            }
        }
        return $middlewares;
    }

    /**
     * 执行中间件
     *
     * @param array $middlewares
     * @param Closure $control
     * @return mixed
     * @Author EricZhao <1091588684@qq.com>
     * @DateTime 2021-08-25 11:46
     */
    public function invokeMiddlewares(array $middlewares, \Closure $control)
    {
        $middlewares = array_reverse($middlewares, true);
        $handle = array_reduce($middlewares, [$this, 'packMiddleware'], $control);
        return call_user_func($handle);
    }

    /**
     * 包装中间件
     *
     * @param array $middle
     * @param Closure $next
     * @Author EricZhao <1091588684@qq.com>
     * @DateTime 2021-08-25 11:46
     */
    public function packMiddleware($next, $middle)
    {
        return function() use ($middle, $next){
            $name = $middle['name'];
            if($name instanceof \Closure){
                return $name($next);
            }else{
                $CI =& get_instance();
                $params = $middle['params'];
                array_unshift($params, $next);
                return call_user_func_array([$CI->$name, 'handle'], $params);
            }            
        };
    }
}