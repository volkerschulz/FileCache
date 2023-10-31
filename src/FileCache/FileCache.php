<?php

namespace volkerschulz;

class FileCache {
    protected Array $options;
    protected String $filename;
    protected Array $attributes;
    protected Array $additional_headers = [];
    protected Array $headers_added = [];

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

    public function addHeader(String $key, String $value) : Bool {
        $key = trim($key);
        $value = trim($value);
        if($key==='' || $value==='') {
            return false;
        }
        $forbidden = [
            'cache-control',
            'last-modified',
            'etag'
        ];
        if(in_array(strtolower($key), $forbidden)) {
            return false;
        }
        $this->additional_headers[] = ['key'=>$key, 'value'=>$value];
        $this->headers_added[] = strtolower($key);
        return true;
    }

    public function respond() : Void {
        if(!$this->isModified()) {
            header('HTTP/1.1 304 Not Modified');
            exit;
        }
        $this->setHeaders();
        $this->setMissingHeaders();
        $this->setAdditionalHeaders();
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

    private function setAdditionalHeaders() : Void {
        foreach($this->additional_headers as $header) {
            header($header['key'] . ': ' . $header['value'], false);
        }
    }

    private function setMissingHeaders() : Void {
        if(!$this->options['add_missing_headers'])
            return;

        if(!in_array('content-type', $this->headers_added)) {
            $mime = $this->getMimeType();
            if(!empty($mime)) {
                $this->addHeader('Content-Type', $mime);
            }
        }
    }

    private function getMimeType() : String|bool {
        if(function_exists('mime_content_type')) {
            return mime_content_type($this->filename);
        }
        return false;
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
            $hash_blocks = [];

            if($this->options['use_checksum']) {
                $hash_blocks[] = hash_file($this->options['hash_algo'], $this->filename);
            } 

            if($this->options['use_filetime']) {
                $hash_blocks[] = dechex(filemtime($this->filename));
            }

            if($this->options['use_filesize']) {
                $hash_blocks[] = dechex(filesize($this->filename));
            }

            $this->attributes['etag'] = implode('-', $hash_blocks);
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
            'use_filetime' => [
                'type' => 'bool',
                'default' => true,
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
            'add_missing_headers' => [
                'type' => 'bool',
                'default' => true,
            ],
        ];

        foreach($options_available as $name=>$option) {
            if(isset($options[$name])) {
                if(!$this->checkType($option['type'], $options[$name])) {
                    throw new \Exception("Illegal value for option '{$name}'");
                }
                $this->options[$name] = $options[$name];
            } else {
                $this->options[$name] = $option['default'];
            }
        }

        // Sanity checks
        if(false === ($this->options['use_filetime'] || $this->options['use_filesize'] || $this->options['use_checksum'])) {
            throw new \Exception("Configuration error: At least one of 'use_filetime' || 'use_filesize' || 'use_checksum' needs to be true");
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