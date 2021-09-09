<?php
namespace Codeigniter\Middleware\Traits;

trait ControlTrait
{
    private $middlewares = [];

    /**
     * 注册中间件
     * ps:只能在构造使用
     *
     * @param mixed $middle
     * @param array $params ['params' => [], 'except' => [], 'only' => []]
     * @Author EricZhao <1091588684@qq.com>
     * @DateTime 2021-08-25 11:49
     */
    final public function middleware($middle, $params = [])
    {
        $default_params = [
            'params' => [],     // 中间件参数
            'except' => [],     // 黑名单
            'only' => []        // 白名单
        ];
        $params = array_merge($default_params, $params);
        // 支持匿名函数
        if($middle instanceof \Closure){
            $params['name'] = $middle;
            $this->middlewares[] = $params;
        }else{
            $name = end(explode('/', $middle));
            $this->load->middleware($middle, $name);
            $params['name'] = $name;
            $this->middlewares[] = $params;
        }
    }

    /**
     * 获取注册的中间件
     *
     * @Author EricZhao <1091588684@qq.com>
     * @DateTime 2021-08-25 11:49
     */
    final public function getMiddlewares()
    {
        return $this->middlewares;
    }

    /**
     * 请求拦截
     *
     * @param string $method
     * @param array $params
     * @Author EricZhao <1091588684@qq.com>
     * @DateTime 2021-08-26 14:46
     */
    public function _remap($method, $params)
    {        
        $middleware = new \codeigniter\Middleware($this);
        $middleware->start($method, $params);
    }
}