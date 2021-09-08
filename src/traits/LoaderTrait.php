<?php
namespace codeigniter\traits;

trait LoaderTrait
{
    protected $_ci_middleware_paths = array(APPPATH, BASEPATH);
    protected $_ci_middlewares = array();

        /**
     * middleware Loader
     *
     * This function lets users load and instantiate models.
     *
     * @param	string	the name of the class
     * @param	string	name for the middleware
     * @param	array	params of the middleware
     * @return	void
     */
    public function middleware($middleware, $name = '', Array $params = array()) {
        if (is_array($middleware)) {
            foreach ($middleware as $babe) {
                $this->middleware($babe);
            }
            return;
        }

        if ($middleware == '') {
            return;
        }

        $path = '';

        // Is the middleware in a sub-folder? If so, parse out the filename and path.
        if (($last_slash = strrpos($middleware, '/')) !== FALSE) {
            // The path is in front of the last slash
            $path = substr($middleware, 0, $last_slash + 1);

            // And the middleware name behind it
            $middleware = substr($middleware, $last_slash + 1);
        }

        if ($name == '') {
            $name = $middleware;
        }

        if (in_array($name, $this->_ci_middlewares, TRUE)) {
            return;
        }

        $CI = & get_instance();
        if (isset($CI->$name)) {
            show_error('The middleware name you are loading is the name of a resource that is already being used: ' . $name);
        }

        $middleware = strtolower($middleware);        
        $parent_path = APPPATH.'core/MY_Middleware.php';
        if(file_exists($parent_path) && !class_exists('MY_Middleware')){
            require_once $parent_path;
        }        

        foreach ($this->_ci_middleware_paths as $mod_path) {
            if (!file_exists($mod_path . 'middlewares/' . $path . $middleware . '.php')) {
                continue;
            }            
            require_once($mod_path . 'middlewares/' . $path . $middleware . '.php');

            $middleware = ucfirst($middleware);

            $CI->$name = new $middleware($params);

            $this->_ci_middlewares[] = $name;
            return;
        }

        // couldn't find the middleware
        show_error('Unable to locate the middleware you have specified: ' . $middleware);
    }  
}