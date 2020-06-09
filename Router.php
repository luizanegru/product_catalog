<?php

class Router
{
    private static $routes = array();

    public static function route($pattern, $callback)
    {
        $pattern = '/^\/' . str_replace('/', '\/', $pattern) . '$/';
        self::$routes[$pattern] = $callback;
    }

    public static function execute($url)
    {
        foreach (self::$routes as $pattern => $callback) {
            if (strpos($url, '?') !== false) {
                $get_params = substr($url, strpos($url, "?"));
            }

            if (isset($get_params) && !empty($get_params)) {
                $url = str_replace($get_params, "", $url);
            }

            if (preg_match($pattern, $url, $params)) {
                array_shift($params);
                return call_user_func_array($callback, array_values($params));
            }
        }
    }
}
