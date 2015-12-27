<?php
/**
 * Created by PhpStorm.
 * User: Bruno Louvem
 * Date: 10/10/2015
 * Time: 14:00
 */

namespace Syph\Routing;


use Syph\DependencyInjection\ServiceInterface;

class UrlMatcher implements ServiceInterface{

    private $patterns;

	public function __construct(){
        $this->initPatterns();
    }

    public function match($url,$collection){

        $url = (substr($url,0,1) == '/')? $url : '/'.$url ;
        foreach ($collection as $name => $route)
        {
            $pattern = '/^' . str_replace('/', '\/', $route->getPattern()) . '$/';
            if (preg_match($pattern, $url, $params))
            {
                array_shift($params);

                return call_user_func_array($route->getCallback(), array_values($params));
            }
        }
        throw new \Exception(sprintf('Route: "%s" not found',$url));

    }

    public function reverse($selected_route,$collection, array $parameters = array()){
        foreach ($collection as $name => $route)
        {
            if($selected_route == $name){
                $url = $route->getPattern();

                if(count($parameters) > 0){
                    $url = preg_replace($this->patterns, $parameters, $url);
                }

                return $url;
            }
        }
        throw new \Exception(sprintf('Route with name: "%s" not found',$selected_route));
    }

    public function isValidRoute(Route $route){
        return $route->getPattern() && is_callable($route->getCallback());
    }

    public function getName()
    {
        return "routing.urlmatcher";
    }

    private function initPatterns()
    {
        $this->patterns = array(
            '/\(\\\w\+\)/',
            '/\(\\\d\+\)/',
        )
        ;
    }
}