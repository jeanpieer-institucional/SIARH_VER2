<?php
/**
 * Router - Sistema de enrutamiento
 */

class Router {
    private $routes = [];
    private $notFound;
    
    public function get($path, $callback) {
        $this->addRoute('GET', $path, $callback);
    }
    
    public function post($path, $callback) {
        $this->addRoute('POST', $path, $callback);
    }
    
    private function addRoute($method, $path, $callback) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'callback' => $callback
        ];
    }
    
    public function setNotFound($callback) {
        $this->notFound = $callback;
    }
    
    public function run() {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = $_SERVER['REQUEST_URI'];
        
        // Remover query string
        $requestUri = strtok($requestUri, '?');
        
        // Canonicalizar URI (evitar problemas con // o barras al final)
        $requestUri = rtrim($requestUri, '/');
        if (empty($requestUri)) {
            $requestUri = '/';
        }
        
        // Remover base path
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $basePath = str_replace('/index.php', '', $scriptName);
        
        if ($basePath !== '' && stripos($requestUri, $basePath) === 0) {
            $requestUri = substr($requestUri, strlen($basePath));
        }
        
        // Asegurar que empiece con /
        if (empty($requestUri) || $requestUri[0] !== '/') {
            $requestUri = '/' . $requestUri;
        }
        
        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod) {
                $pattern = $this->convertToRegex($route['path']);
                
                if (preg_match($pattern, $requestUri, $matches)) {
                    array_shift($matches);
                    return call_user_func_array($route['callback'], $matches);
                }
            }
        }
        
        // Ruta no encontrada
        if ($this->notFound) {
            return call_user_func($this->notFound);
        }
        
        http_response_code(404);
        echo "404 - Página no encontrada";
    }
    
    private function convertToRegex($path) {
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([a-zA-Z0-9_-]+)', $path);
        return '#^' . $pattern . '$#';
    }
}
