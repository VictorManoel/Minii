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
        $view = $this->compilerIf($view, $data);
        $view = $this->compilerFor($view, $data);
        $view = $this->loadIncludes($view, $data);
        $view = $this->variables($view, $data);
        $view = $this->beautify->format($view);
        
        return $view;
    }
    
    private function compilerIf($html, $data) {
        
        $re = '/@if\((.*)\)([\s\S]*?)@endif/';
        preg_match_all($re, $html, $matches, PREG_SET_ORDER, 0);
        
        $patterns = ['/\@elif\((.*)\)/', '/\@else/'];
        $replacements = ['\';}else if(\1){echo \'', '\';}else{echo \''];
        
        foreach ($matches as $matche) {
            
            $functionStr = "if($matche[1]){ echo '$matche[2]';}";
            $functionStr = preg_replace($patterns, $replacements, $functionStr);
            
            $str = $this->compilerFunctions($functionStr, $data);
            $html = str_replace($matche[0], $str, $html);   
        }
        
        return $html;
    }
    
    private function compilerFor($html, $data) {
        
        $re = '/@for\((.*)\)([\s\S]*?)@endfor/';
        preg_match_all($re, $html, $matches, PREG_SET_ORDER, 0);
        
        foreach ($matches as $matche) {
            
            preg_match('/\$(\w+)/',$matche[1], $var);
            $matche[2] = preg_replace('/\{\{\s*\\'.$var[0].'\s*\}\}/', "'.$var[0].'", $matche[2]);
            
            $functionStr = "for($matche[1]){ echo '$matche[2]';}";
            $str = $this->compilerFunctions($functionStr, $data);
            $html = str_replace($matche[0], $str, $html);   
        }
        
        return $html;
    }
    
    private function compilerFunctions ($compilerFunction, $compilerData) {
        
        ob_start();
            if(is_array($compilerData)) extract($compilerData);
            eval($compilerFunction);
            $compilerFunction = ob_get_contents();
        ob_end_clean();
        
        return $compilerFunction;
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
                
                $patterns[] = '/\{\{\s*\$' . trim($pattern) . '\s*\}\}/';
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