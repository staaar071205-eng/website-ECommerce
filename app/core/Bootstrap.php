<?php
namespace App\Core;

class Bootstrap
{
    public function __construct()
    {
        $url = $_GET['page'] ?? $this->getRequestUri();

        if ($url) {
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = trim($url, '/');
            $url = explode('/', $url);

            $page = ucfirst(array_shift($url));

            if (file_exists(ROOT . "app/controllers/" . $page . ".php")) {
                $class = "App\\Controllers\\" . $page;
                $controller = new $class;

                $action = $url ? array_shift($url) : 'index';

                if (method_exists($controller, $action)) {
                    $params = array_values($url);
                    call_user_func_array([$controller, $action], $params);
                } else {
                    $controller->index();
                }
            } else {
                $class = "App\\Core\\Error";
                $controller = new $class();
                $controller->fileNotFound();
            }
        } else {
            $class = "App\\Controllers\\Index";
            $controller = new $class();
            $controller->index();
        }
    }

    private function getRequestUri()
    {
        $uri = $_SERVER['REQUEST_URI'];
        $uri = rawurldecode(parse_url($uri, PHP_URL_PATH) ?? '');

        $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
        if ($base !== '' && stripos($uri, $base) === 0) {
            $uri = substr($uri, strlen($base));
        }

        $uri = preg_replace('#^/?index\.php#i', '', $uri);

        return trim($uri, '/');
    }
}