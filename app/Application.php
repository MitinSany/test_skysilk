<?php

namespace app;

use app\exception\Exception;
use app\exception\JsonFormatException;
use app\model\User;
use \PDO;
use \app\helper\Auth;
use \FastRoute\RouteCollector;
use function \FastRoute\simpleDispatcher;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use \Zend\Diactoros\Request;
use \Zend\Diactoros\ServerRequestFactory;
use \Zend\Diactoros\Response;
use \Zend\Diactoros\Response\SapiEmitter;
use \Zend\Diactoros\Response\JsonResponse;
use \FastRoute\Dispatcher;

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
     * @var \FastRoute\Dispatcher
     */
    protected $dispatcher;

    /**
     * @var Application
     */
    public static $app;

    public function __construct(array $config, array $routes)
    {
        self::$app = $this;
        $this->config = $config;
        $user = new User();
        $request = $this->getRequest();
        $this->auth = new Auth($user, $request);

        $routeDefinitionCallback = function (RouteCollector $r) use ($routes) {
            foreach ($routes as $route) {
                $r->addRoute($route[0], $route[1], $route[2]);
            }
        };

        $this->dispatcher = simpleDispatcher($routeDefinitionCallback);
    }

    public function db()
    {
        if (empty($this->db)) {
            $this->db = new PDO('sqlite:' . $this->config['db_file']);
            $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        }
        return $this->db;
    }

    protected function getRequest(): RequestInterface
    {
        return ServerRequestFactory::fromGlobals(
            $_SERVER,
            $_GET,
            $_POST,
            $_COOKIE,
            $_FILES
        );
    }

    public function run()
    {
        $request = $this->getRequest();
        $response = new Response();

        $routeInfo = $this->dispatcher->dispatch($request->getMethod(), (string)$request->getUri()->getPath());
        try {
            switch ($routeInfo[0]) {
                case Dispatcher::NOT_FOUND:
                    $response = $response->withStatus(404);

                    $page = $this->render('message', [
                        'title' => 'Error',
                        'message' => '404 - Page not found',
                        'style' => 'warning'
                    ]);
                    $response->getBody()->write($page);
                    break;

                case Dispatcher::METHOD_NOT_ALLOWED:
                    $page = $this->render('message', [
                        'title' => 'Error',
                        'message' => '405 - Method not allowed',
                        'style' => 'warning'
                    ]);
                    $response->getBody()->write($page);
                    $response = $response->withStatus(405);
                    break;

                case Dispatcher::FOUND:
                    $handler = $routeInfo[1];
                    $vars = $routeInfo[2];
                    $controller = new $handler[0]();
                    $method = $handler[1];
                    $response = $controller->$method($request, $response, $vars);
                    break;

            }
        } catch (JsonFormatException $e) {
            $data = ['success' => false, 'message' => $e->getMessage()];
            $response = new JsonResponse($data, $e->getCode() > 0 ? $e->getCode() : 500);
        } catch (Exception $e) {
            $page = $this->render('message', [
                'title' => 'Error',
                'message' => $e->getMessage(),
                'style' => 'danger'
            ]);
            $response->getBody()->write($page);
            $response = $response->withStatus(500);
        }

        $this->emit($response);
    }

    public function emit(ResponseInterface $response)
    {
        $emitter = new SapiEmitter();
        $emitter->emit($response);
        exit;
    }

    public function render(string $template, array $param = []): string
    {
        $templateFile = realpath(__DIR__ . '/view/' . $template . '.php');

        ob_start();
        if (file_exists($templateFile)) {
            include __DIR__ . '/../app/view/layout.php';
        } else {
            $this->render('message', [
                'title' => 'Error',
                'message' => 'Template ' . $template . ' not found',
                'style' => 'danger'
            ], 500);
        }
        return ob_get_clean();
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