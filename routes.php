<?php
class Route {
    private const METHOD_GET = "GET";
    private const METHOD_POST = "POST";

    static function get(string $path, $callback)
    {
        $uri = strlen($_SERVER['REQUEST_URI']) == 1 ? $_SERVER['REQUEST_URI'] : rtrim($_SERVER['REQUEST_URI'], '/');
        $uriArr = self::sub_url($_SERVER['REQUEST_URI']);

        if (self::METHOD_GET !== $_SERVER['REQUEST_METHOD']) return;
        if (self::sub_url($path, 0) !== $uriArr[0]) return;

        if ($uri === $path) {
            return call_user_func($callback);
        }

        $patterns = array('/\//', '/{\w+}/');
        $replacements = array('\/', '\w+');
        $regex = '/'.preg_replace($patterns, $replacements, $path).'/';   

        if (preg_match($regex, $uri, $matches)) {
            if ($uri === $matches[0]) {
                $result = array_diff(self::sub_url($path), self::sub_url($uri));
                $params = array();

                foreach ($result as $key => $value) {
                    $params[$value] = $uriArr[$key];
                }

                call_user_func_array($callback, $params);
            }
        }
    }

    public static function sub_url(string $path, $key = -1)
    {
        return preg_match_all('/\w+/', $path, $matches) ? ($key == -1 ? $matches[0] : $matches[0][$key]) : '';
    }
}