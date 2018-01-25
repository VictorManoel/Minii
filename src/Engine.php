<?php

namespace Minii;

class Engine {
    
    private $viewsPath, $includesPath, $globals;
    private $beautify;
    
    public function __construct ($config) {
        
        $this->viewsPath = $config['views'];
        $this->includesPath = $config['includes'];
        $this->globals = $config['globals'];
        
        $this->beautify = new Beautify();
    }
    
    public function compiler ($viewPath, $data) {
        
        if (is_array($this->globals)) {
            
            if (is_array($data)) {
                
                $data = array_merge($this->globals, $data);
                
            } else {
                
                $data = $this->globals;   
            }
        }
        
        $view = $this->loader($viewPath);
        $view = $this->loadIncludes($view, $data);
        $view = $this->variables($view, $data);
        
        return $this->beautify->format($view);
    }
    
    private function loadIncludes ($view, $data) {
        
        $re = '/\{\%\s*(\w+)\s*\%\}/';
        preg_match_all($re, $view, $includes, PREG_SET_ORDER, 0);
        $includePath = $this->includesPath . DIRECTORY_SEPARATOR;
        
        foreach($includes as $include) {
            
            $path = $includePath . $include[1] . '.html';
            
            if(file_exists($path)) {
                
                $str = $this->loader($path);
                $view = str_replace($include[0], $str, $view);
            }
        }
        
        return $view;
    }
    
    private function variables ($view, $data) {
        
        $patterns = [];
        $replacements = [];
        
        if ($data && is_array($data)) {
            
            foreach ($data as $pattern => $replacement) {
                
                $patterns[] = '/\{\(\s*\$' . trim($pattern) . '\s*\)\}/';
                $replacements[] = $replacement;
            }
            
            $view = preg_replace($patterns, $replacements, $view);
        }
        
        return $view;
    }
    
    private function loader ($path) {
        
        ob_start();
            require($path);
            $view = ob_get_contents();
        ob_end_clean();
        return $view;
    }
}