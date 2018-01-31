<?php 

namespace Minii;

class Beautify {
    
    public function format ($html) {
        
        $html = str_replace(["\n", "\r", "\t"], ['', '', ' '], $html);
        $elements = preg_split('/(<.+>)/U', $html, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        
        $elem = []; 
        foreach ($elements as $element) if (trim($element) != null) $elem[] = $element;
        
        $output = implode('', $this->formatDom($elem));
        $output = $this->replacement($output);
        
        return trim($output);
    }
    
    private function formatDom ($elem) {
        
        $indent = 0;
        $output = [];
        $indentWith = '    ';
        
        $dom = $this->parseDom($elem);
        foreach ($dom as $index => $element){
            
            if ($element['opening']) {
                
                $output[] = "\n" . str_repeat($indentWith, $indent) . trim($element['content']);
                if (!in_array($element['type'], ['html','link','img','meta','input'])) ++$indent;
                
            } else if ($element['standalone']) {
                
                $output[] = "\n" . str_repeat($indentWith, $indent) . trim($element['content']);
                
            } else if ($element['closing']) {
                
                --$indent;
                $lf = "\n" . str_repeat($indentWith, abs($indent));
                if (isset($dom[$index - 1]) && $dom[$index - 1]['opening']) $lf = '';
                $output[] = $lf . trim($element['content']);
                
            } else if ($element['text']) {
                
                $output[] = "\n" . str_repeat($indentWith, $indent) . preg_replace('/ [ \t]*/', ' ', $element['content']);
                
            } else if ($element['comment']) {
                
                $output[] = "\n" . str_repeat($indentWith, $indent) . trim($element['content']);
            }
        }
        
        return $output;
    }
    
    private function parseDom(Array $elements) {
        
        $dom = [];
        foreach ($elements as $element) {
            
            $isText = false;
            $isComment = false;
            $isClosing = false;
            $isOpening = false;
            $isStandalone = false;
            $currentElement = trim($element);
            
            if (strpos($currentElement, '<!') === 0) {
                
                $isComment = true;
                
            } else if (strpos($currentElement, '</') === 0) {
                
                $isClosing = true;
                
            } else if ($this->standaloneTags($currentElement)) {
                
                $isStandalone = true;
                
            } else if (strpos($currentElement, '<') === 0) {
                
                $isOpening = true;
                
            } else {
                
                $isText = true;
            }
            
            $dom[] = [
                'text' => $isText,
                'comment' => $isComment,
                'closing' => $isClosing,
                'opening' => $isOpening,
                'standalone' => $isStandalone,
                'content' => $element,
                'type' => preg_replace('/^<\/?(\w+)[ >].*$/U', '$1', $element)
            ];
        }
        
        return $dom;
    }
    
    private function standaloneTags($currentElement) {
        
        $StandaloneTags = '/^<(input|link|area|base|br|col|command|embed|';
        $StandaloneTags .= 'hr|img|keygen|meta|param|source|track|wbr) /';
        
        $openTag = preg_match($StandaloneTags, $currentElement);
        $endTag = preg_match('/\/$/', $currentElement);
        
        return ($openTag || $endTag) ? true : false;
    }
    
    private function replacement($html) {
        
        $patterns = [
            '/<title>\s*(.+)\s*<\/title>/',
            '/<(i|a|p|span) (.*)>\s*(.{0,10})\s*<\/(.{1,4})>/', 
            '/<\/body>\s*<\/html>/'
        ];
        
        $replace = ['<title>\1</title>', '<\1 \2>\3</\1>', "</body>\n</html>"];
        
        return preg_replace($patterns, $replace, $html);
    }
}