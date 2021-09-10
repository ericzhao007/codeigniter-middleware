# codeigniter-middleware

> CI2框架中间件实现   

### 1. 在自己的控制器基类中加入以下
```php
class MY_Controller extends CI_Controller
{
    use Codeigniter\Middleware\Traits\ControlTrait;
}
```

### 2. 在框架应用目录的`core`下创建自己的[subclass_prefix]_Loader.php
```php
class MY_Loader extends CI_Loader
{
    use Codeigniter\Middleware\Traits\LoaderTrait;
}
```

### 3. 现在你可以在应用下的`middlewares`目录下创建自己的中间件啦
```php
class throttle_middleware extends Codeigniter\Middleware\Abstracts\CI_Middleware
{
    public function handle(\Closure $next, $maxAttempts = 60, $decayMinutes = 1)
    {
        return $next();
    }
}
```

```php
class Test extends MY_Controller
{
    public function init()
    {
        // 中间件
        $this->middleware('csrf_middleware');

        // 带参数的中间件
        $this->middleware('throttle_middleware', [
            'params' => [60,10],
            'except' => ['index']
        ]);

        // 匿名中间件
        $this->middleware(function($next){
            if(true){
                return ['error_code' => 1, 'error_msg' => '我拦住你了'];
            }
            return $next();
        }, [
            'only' => ['index']
        ]);

        // 带后置操作的匿名中间件
        $this->middleware(function($next){
            echo 'this is test route.';
            $response = $next();
            echo "后置中间件";
            return $response;
        }, [
            'except' => ['index'],
        ]);
        
    }

    public function index()
    {
        echo "hello world";
    }

    public function test1()
    {
        echo "test1";
    }

    public function test2()
    {
        echo "test2";
    }

    public function notify()
    {
        echo date('Y-m-d H:i:s');
    }
    
}
```