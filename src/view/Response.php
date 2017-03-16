<?php
namespace flyeralarm\ResellerApiExample\view;


class Response {

    private $header;

    private $text = null;

    public function addLine($line){
        if($this->text !== null ){
            $this->text = $this->text . "<br>\n";
        }

        $this->text = $this->text.$line;
    }

    public function setHeader($header){
        $this->header = $header;
    }

    public function getHeader(){
        return $this->header;
    }

    public function getText(){
        return $this->text;
    }


}