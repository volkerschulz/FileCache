<?php

namespace volkerschulz;

class FileCache {
    protected Array $options;
    protected String $filename;
    protected Array $attributes;

    function __construct(String $filename, Array $options=[]) {
        if(!file_exists($filename)) {
            throw new \Exception("File '{$filename}' not found");
        }
        $this->filename = $filename;
        $this->setOptionsInit($options);
    }

    public function getOptions() : Array {
        return $this->options;
    }

    public function respond() : Void {
        if(!$this->isModified()) {
            header('HTTP/1.1 304 Not Modified');
            exit;
        }
        $this->setHeaders();
        readfile($this->filename);
        exit;
    }

    private function setHeaders() : Void {
        if(empty($this->options['fresh_for'])) {
            header('Cache-Control: no-cache');
        } else {
            header('Cache-Control: max-age=' . $this->options['fresh_for'] . ', must-revalidate');
        }
        
        if($this->options['use_etag']) 
            header('ETag: "' . $this->getEtag() . '"');
        header('Last-Modified: ' . $this->getModificationTimeFormatted());
    }

    private function isModified() : Bool {
        if($this->options['use_etag'] && !empty($_SERVER['HTTP_IF_NONE_MATCH'])) {
            if(trim($_SERVER['HTTP_IF_NONE_MATCH'], '"') === $this->getEtag()) {
                return false;
            }
        }
        if(!empty($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            if(trim($_SERVER['HTTP_IF_MODIFIED_SINCE'], '"') === $this->getModificationTimeFormatted()) {
                return false;
            }
        }
        return true;
    }

    private function getEtag() : String {
        if(empty($this->attributes['etag'])) {
            $this->attributes['etag'] = '';
            if($this->options['use_checksum']) {
                $this->attributes['etag'] = hash_file($this->options['hash_algo'], $this->filename);
            } else {
                $this->attributes['etag'] = dechex(filemtime($this->filename));
                if($this->options['use_filesize']) {
                    $this->attributes['etag'] .= '-' . dechex(filesize($this->filename));
                }
            }
        }
        return $this->attributes['etag'];
    }

    private function getModificationTimeFormatted() {
        if(empty($this->attributes['last_modified'])) {
            $this->attributes['last_modified'] = gmdate('D, d M Y H:i:s T', filemtime($this->filename));
        }
        return $this->attributes['last_modified'];
    }

    private function setOptionsInit(Array $options) : Void {
        $options_available = [
            'use_checksum' => [
                'type' => 'bool',
                'default' => false,
            ],
            'hash_algo' => [
                'type' => 'hash_algo',
                'default' => 'crc32',
            ],
            'use_filesize' => [
                'type' => 'bool',
                'default' => true,
            ],
            'use_etag' => [
                'type' => 'bool',
                'default' => true,
            ],
            'fresh_for' => [
                'type' => 'int',
                'default' => 0,
            ],
        ];

        foreach($options_available as $name=>$option) {
            if(!empty($options[$name])) {
                if(!$this->checkType($option['type'], $options[$name])) {
                    throw new \Exception("Illegal value for option '{$name}'");
                }
                $this->options[$name] = $options[$name];
            } else {
                $this->options[$name] = $option['default'];
            }
        }
    }

    private function checkType(String $type, mixed $value) : Bool {
        switch($type) {
            case 'bool':
                return is_bool($value);
            case 'int':
                return is_int($value);
            case 'hash_algo':
                return in_array($value, hash_algos());
        }
    }
}