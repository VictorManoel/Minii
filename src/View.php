<?php

namespace Minii;

class View {
    
    private $viewPath, $includePath, $engine;
    
    public function __construct ($config) {
        
        $config = $this->init($config);
        
        if ($config) $this->engine = new Engine($config);
    }
    
    private function init ($config) {
        
        if(isset($config['views'])) {
            
            $this->viewsPath = $this->normalizePath($config['views']);
            $config['views'] = $this->viewsPath;
            
        } else {
            
            throw new \Exception("The View path could not be found.");
            return false;
        }
        
        if(isset($config['includes'])) {
            
            $this->includePath = $this->normalizePath($config['includes']);
            $config['includes'] = $this->includePath;
            
        } else {
            
            $this->includesPath = $this->viewsPath;
        }
        
        $config['globals'] = isset($config['globals']) ? $config['globals'] : null;
        
        return $config;
    }
    
    public function load ($view, $data = null) {
        
        $ds = DIRECTORY_SEPARATOR;
        $viewPath = $this->includePath. $ds. trim($view). '.html';
        
        if(file_exists($viewPath)) {
            
            return $this->engine->compiler($viewPath, $data);
            
        } else {
            
            throw new \Exception("The file \"$view\" in \"$viewPath\" could not be found.");
        }
    }
    
    public function render ($view, $data = null) {
        
        $ds = DIRECTORY_SEPARATOR;
        $viewPath = $this->viewsPath. $ds. trim($view). '.html';
        
        if(file_exists($viewPath)) {
            
            return $this->engine->compiler($viewPath, $data);
            
        } else {
            
            throw new \Exception("The file \"$view\" in \"$viewPath\" could not be found.");
        }
    }
    
    private function normalizePath ($path) {
        
        $path = substr($path, -1) === '/' ? substr($path, 0, -1) : $path;
        return str_replace('/', DIRECTORY_SEPARATOR, $path);
    }
}