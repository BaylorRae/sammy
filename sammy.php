<?php
/**
 * Sammy - A bare-bones PHP version of the Ruby Sinatra framework.
 *
 * @version		1.0
 * @author		Dan Horrigan
 * @license		MIT License
 * @copyright	2010 Dan Horrigan
 */

function get($route, $callback) {
	Sammy::process($route, $callback, 'GET');
}

function post($route, $callback) {
	Sammy::process($route, $callback, 'POST');
}

function put($route, $callback) {
	Sammy::process($route, $callback, 'PUT');
}

function delete($route, $callback) {
	Sammy::process($route, $callback, 'DELETE');
}

function ajax($route, $callback) {
	Sammy::process($route, $callback, 'XMLHttpRequest');
}

class Sammy {
	
	public static $route_found = false;
	
	public $uri = '';

	public $segments = '';

	public $method = '';
	
	public $format = '';

	public static function instance() {
		static $instance = null;
		
		if( $instance === null ) {
			$instance = new Sammy;
		}
		
		return $instance;
	}

	public static function run() {
		if( !static::$route_found ) {
			echo 'Route not defined!';
		}
		
		ob_end_flush();
	}

	public static function process($route, $callback, $type) {
		$sammy = static::instance();
		
    // Check for ajax
		if( $type == 'XMLHttpRequest' )
		  $sammy->method = isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? $_SERVER['HTTP_X_REQUESTED_WITH'] : 'GET';
		
		if( static::$route_found || (!preg_match('@^'.$route.'(?:\.(\w+))?$@uD', $sammy->uri, $matches) || $sammy->method != $type) ) {
			return false;
		}
				
		$sammy->format = (!empty($matches[1])) ? $matches[1] : null;
		
		static::$route_found = true;
		echo $callback($sammy);
	}
	
	public function __construct() {
		ob_start();
		$this->uri = $this->get_uri();
		$this->segments = explode('/', trim($this->uri, '/'));
		$this->method = $this->get_method();
	}

	public function segment($num) {
		return isset($this->segments[$num - 1]) ? $this->segments[$num - 1] : null;
	}

	protected function get_method() {
		return isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
	}
	
	protected function get_uri($prefix_slash = true) {
	    if( isset($_SERVER['PATH_INFO']) ) {
	        $uri = $_SERVER['PATH_INFO'];
	    }elseif( isset($_SERVER['REQUEST_URI']) ) {
	        $uri = $_SERVER['REQUEST_URI'];
	        
	        if( strpos($uri, $_SERVER['SCRIPT_NAME']) === 0 ) {
	            $uri = substr($uri, strlen($_SERVER['SCRIPT_NAME']));
	        }elseif( strpos($uri, dirname($_SERVER['SCRIPT_NAME'])) === 0 ) {
	            $uri = substr($uri, strlen(dirname($_SERVER['SCRIPT_NAME'])));
	        }

	        // This section ensures that even on servers that require the URI to be in the query string (Nginx) a correct
	        // URI is found, and also fixes the QUERY_STRING server var and $_GET array.
	        if( strncmp($uri, '?/', 2) === 0 ) {
	            $uri = substr($uri, 2);
	        }
	        
	        $parts = preg_split('#\?#i', $uri, 2);
	        $uri = $parts[0];
	        
	        if( isset($parts[1]) ) {
	            $_SERVER['QUERY_STRING'] = $parts[1];
	            parse_str($_SERVER['QUERY_STRING'], $_GET);
	        }else {
	            $_SERVER['QUERY_STRING'] = '';
	            $_GET = array();
	        }
	        $uri = parse_url($uri, PHP_URL_PATH);
	    }else {
	        // Couldn't determine the URI, so just return false
	        return false;
	    }

	    // Do some final cleaning of the URI and return it
	    return ($prefix_slash ? '/' : '').str_replace(array('//', '../'), '/', trim($uri, '/'));
	}
	
	public function format($name, $callback) {
	  $sammy = static::instance();
	  if( !empty($sammy->format) && $name == $sammy->format )
	    echo $callback($sammy);
	  else
	    return false;
	}
}

$sammy = Sammy::instance();