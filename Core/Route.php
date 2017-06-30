<?php
namespace Core;

use Modules\Base;

/**
 *  Class for routing on the site
 */
class Route
{

    static $_instance; // Singletone static variable

    /**
     *  Singletone static method
     */
    static function factory()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }


    protected $_params; // Parameters for current page
    protected $_controller; // Current controller
    protected $_action; // Current action
    protected $_module; // Current module


    /**
     *  Get all parameters on current page
     */
    public static function params()
    {
        return Route::factory()->getParams();
    }


    /**
     *  Get one parameter by alias
     * @param string $key - alias for parameter we need
     * @return mixed      - parameter $key from $_params
     */
    public static function param($key)
    {
        return Route::factory()->getParam($key);
    }


    /**
     *  Get current controller
     */
    public static function controller()
    {
        return Route::factory()->getController();
    }


    /**
     *  Get current action
     */
    public static function action()
    {
        return Route::factory()->getAction();
    }


    /**
     *  Get current module
     */
    public static function module()
    {
        return Route::factory()->getModule();
    }


    /**
     *  Real function to get all parameters on current page
     */
    public function getParams()
    {
        return $this->_params;
    }


    /**
     *  Set parameter to parameters array
     * @param string $key - alias for parameter we set
     * @param string $value - value for parameter we set
     */
    public function setParam($key, $value)
    {
        $this->_params[$key] = $value;
    }


    /**
     *  Real function to get one parameter by alias
     * @param string $key - alias for parameter we need
     * @return mixed      - parameter $key from $_params if exists or NULL if doesn't exist
     */
    public function getParam($key)
    {
        return Arr::get($this->_params, $key, null);
    }


    /**
     *  Real function to get controller
     */
    public function getController()
    {
        return $this->_controller;
    }


    /**
     *  Real function to get action
     */
    public function getAction()
    {
        return $this->_action;
    }


    /**
     *  Real function to get module
     */
    public function getModule()
    {
        return $this->_module;
    }


    /**
     * Set action
     * @param string $fakeAction
     * @return string
     */
    public function setAction($fakeAction)
    {
        return $this->_action = $fakeAction;
    }


    public $_defaultAction = 'index'; // Default action
    public $_uri; // Current URI
    public $_modules = []; // Modules we include to project

    protected $_routes = []; // Array with routes on the full site
    protected $_regular = 'a-zA-Z0-9-_\\\[\]\{\}\:\,\*'; // List of good signs in regular expressions in routes


    /**
     *  Foreplay
     */
    function __construct()
    {
        $this->setURI();
        $this->setModules();
        $this->setRoutes();
        $this->run();
        $this->setLanguage();
    }


    /**
     *  Uses for multilang sites
     */
    protected function setLanguage()
    {
        if (class_exists('I18n')) {
            if (isset($this->_params['lang'])) {
                \I18n::lang($this->_params['lang']);
            } else {
                $this->_params['lang'] = \I18n::$lang;
            }
        }
    }


    /**
     *  Set current URI
     */
    protected function setURI()
    {
        if (!empty($_SERVER['REQUEST_URI'])) {
            $tmp = rtrim(Arr::get($_SERVER, 'REQUEST_URI'), '/');
            if ($tmp[0] == '/') {
                $tmp = substr($tmp, 1, strlen($tmp) - 1);
            }
            $tmp = explode('?', $tmp);
            $this->setGET(Arr::get($tmp, 1));
            return $this->_uri = $tmp[0];
        }
        if (!empty($_SERVER['PATH_INFO'])) {
            $tmp = rtrim(Arr::get($_SERVER, 'PATH_INFO'), '/');
            if ($tmp[0] == '/') {
                $tmp = substr($tmp, 1, strlen($tmp) - 1);
            }
            $tmp = explode('?', $tmp);
            $this->setGET(Arr::get($tmp, 1));
            return $this->_uri = $tmp[0];
        }
        if (!empty($_SERVER['QUERY_STRING'])) {
            $tmp = rtrim(Arr::get($_SERVER, 'QUERY_STRING'), '/');
            if ($tmp[0] == '/') {
                $tmp = substr($tmp, 1, strlen($tmp) - 1);
            }
            $tmp = explode('?', $tmp);
            $this->setGET(Arr::get($tmp, 1));
            return $this->_uri = $tmp[0];
        }
    }


    /**
     *  Set GET parameters
     * @param string $get - all after "?" in current URI
     */
    protected function setGET($get)
    {
        $get = explode('&', $get);
        foreach ($get as $element) {
            $g = explode('=', $element);
            $_GET[Arr::get($g, 0)] = rawurldecode(Arr::get($g, 1));
        }
    }


    /**
     *  Generate array with modules we need in this project
     */
    protected function setModules()
    {
        $modules = Config::get('modules');
        if (APPLICATION == 'frontend' || APPLICATION == 'backend') {
            if (isset($modules[APPLICATION]) && is_array($modules[APPLICATION])) {
                $this->_modules = $modules[APPLICATION];
            }
        }
    }


    /**
     *  Generate array with default routes and routes in all modules we include
     */
    protected function setRoutes()
    {
        if (APPLICATION == 'backend') {
            $modulesFolder = HOST . DS . 'Wezom' . DS . 'Modules' . DS;
        } else {
            $modulesFolder = HOST . DS . 'Modules' . DS;
        }
        // Routes from modules
        foreach ($this->_modules as $module) {
            $path = $modulesFolder . $module . DS . 'Routing.php';
            if (file_exists($path)) {
                $config = require_once $path;
                if (is_array($config) && !empty($config)) {
                    $this->_routes += $config;
                }
            }
        }
        // Default route
        $this->_routes['<module>/<controller>/<action>'] = '<module>/<controller>/<action>';
    }


    /**
     *  Generate controller, action, parameters from url
     */
    protected function run()
    {
        foreach ($this->_routes as $pattern => $route) {
            // Check if pattern same as current URI
            if ($pattern == $this->_uri) {
                return $this->set($route);
            }
            if (count(explode('/', $this->_uri)) !== count(explode('/', $pattern))) {
                continue;
            }
            // Generate pattern for all link
            if (!preg_match_all('/<.*>/U', $pattern, $matches)) {
                continue;
            }
            $matches = $matches[0];
            $array = [];
            $from = ['/'];
            $to = ['\/'];

            foreach ($matches AS $match) {
                $tmp = substr($match, 1, strlen($match) - 2);
                $tmp = explode(':', $tmp);
                $array[] = ['url' => $match, 'parameter' => $tmp[0], 'pattern' => isset($tmp[1]) ? '(' . $tmp[1] . ')' : '(.*)'];

                $from[] = $match;
                $to[] = isset($tmp[1]) ? '(' . $tmp[1] . ')' : '(.*)';
            }
            $_pattern = str_replace($from, $to, $pattern);

            if (!preg_match('/^' . $_pattern . '$/', $this->_uri, $matches)) {
                continue;
            }
            unset($matches[0]);
            if (count($matches) !== count($array)) {
                continue;
            }
            $params = []; // parameters list for current route
            foreach ($array AS $key => $el) {
                $params[$el['parameter']] = $matches[$key + 1];
            }
            return $this->set($route, $params);
        }
        return Config::error();
    }


    /**
     *  Set route parameters
     * @param string $route - current route
     * @param array $params - parameters for current route
     * @return                 boolean
     */
    protected function set($route, $params = [])
    {
        $array = explode('/', $route);
        // Set module
        if (isset($params['module'])) {
            $this->_module = Arr::get($params, 'module', null);
            unset($params['module']);
        } else {
            $this->_module = Arr::get($array, 0, null);
        }
        // Set controller
        if (isset($params['controller'])) {
            $this->_controller = Arr::get($params, 'controller', null);
            unset($params['controller']);
        } else {
            $this->_controller = Arr::get($array, 1, null);
        }
        // Set action
        if (isset($params['action'])) {
            $this->_action = Arr::get($params, 'action', null);
            unset($params['action']);
        } else {
            $this->_action = Arr::get($array, 2, $this->_defaultAction);
        }
        // Set else parameters
        foreach ($params as $key => $value) {
            $this->setParam($key, $value);
        }
        return true;
    }


    /**
     *  Start site. Initialize controller
     */
    public function execute()
    {
        $module = ucfirst(Route::module());
        $controller = ucfirst(Route::controller());
        $action = Route::action();
        if (APPLICATION == 'backend') {
            $path[] = 'Wezom';
        }
        $path[] = 'Modules';
        if ($module) {
            $path[] = $module;
        }
        $path[] = 'Controllers';
        $path[] = $controller;
        if (file_exists(HOST . DS . implode(DS, $path) . '.php')) {
            return $this->start($path, $action);
        }
        unset($path[count($path) - 2]);
        if (file_exists(HOST . DS . implode(DS, $path) . '.php')) {
            return $this->start($path, $action);
        }
        return $this->error();
    }


    /**
     *  Run controller->action
     */
    protected function start($path, $action)
    {
        $action .= 'Action';
        $controller = implode('\\', $path);
        $controller = new $controller;
        if (!method_exists($controller, $action)) {
            return $this->error();
        }
        $controller->before();
        if (Config::get('error')) {
            return $this->error();
        }
        $token = \Profiler::start('Profiler', 'Center');
        $controller->{$action}();
        if (Config::get('error')) {
            return $this->error();
        }
        \Profiler::stop($token);
        $controller->after();
        if (Config::get('error')) {
            return $this->error();
        }
        return true;
    }

    /**
     * Page 404
     * @return bool
     */
    protected function error()
    {
        Config::error();
        $controller = new Base();
        $controller->before();
        $controller->after();
        return false;
    }

}