<?php

namespace app;

use app\model\User;
use \PDO;
use \app\helper\Auth;

class Application
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $routes;

    /**
     * @var \PDO
     */
    protected $db;

    /**
     * @var Auth
     */
    protected $auth;

    /**
     * @var Application
     */
    public static $app;

    public function __construct(array $config = [])
    {
        self::$app = $this;
        $this->config = $config;
        $user = new User();
        $this->auth = new Auth($user);
    }

    protected function addRoute(string $method, string $route, $handler)
    {
        $this->routes[$method][$route] = $handler;
    }

    public function get(string $route, $handler)
    {
        $this->addRoute('GET', $route, $handler);
    }

    public function post(string $route, $handler)
    {
        $this->addRoute('POST', $route, $handler);
    }

    public function db()
    {
        if (empty($this->db)) {
            $this->db = new \PDO('sqlite:' . $this->config['db_file']);
            $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        }
        return $this->db;
    }

    public function run()
    {
        $path = strpos($_SERVER['REQUEST_URI'], '?') > 0
            ? explode('?', $_SERVER['REQUEST_URI'])[0]
            : $_SERVER['REQUEST_URI'];
        if ($this->routes[$_SERVER['REQUEST_METHOD']][$path]) {
            $handler = $this->routes[$_SERVER['REQUEST_METHOD']][$path];
            if (gettype($handler) == 'object') {
                $handler();
            } elseif (gettype($handler) == 'array') {
                $class = new $handler[0];
                $method = $handler[1];
                $class->$method();
            } else {
                throw new \Exception('Unknown type of operand: ' . $handler);
            }
        } else {
            $this->render('message', [
                'title' => 'Error',
                'message' => "Method:Route not found: <pre>{$_SERVER['REQUEST_METHOD']}:{$_SERVER['REQUEST_URI']}</pre>",
                'style' => 'danger'
            ], 500);
        }
    }

    public function render(string $template, array $param = [], int $exitCode = 200, $asString = false)
    {
        $templateFile = realpath(__DIR__ . '/view/' . $template . '.php');

        if ($exitCode != 200) {
            http_response_code($exitCode);
        }

        if (file_exists($templateFile)) {
            include __DIR__ . '/../app/view/layout.php';
        } else {
            $this->render('message', [
                'title' => 'Error',
                'message' => 'Template ' . $template . ' not found',
                'style' => 'danger'
            ], 500);
        }
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getAuth()
    {
        return $this->auth;
    }
}