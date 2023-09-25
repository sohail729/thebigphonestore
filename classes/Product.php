<?php


class Product
{
    public $make;
    public $model;
    public $colour;
    public $capacity;
    public $network;
    public $grade;
    public $condition;

    public $data;
    public $headers;
    public $requiredFields;

    public function __construct($data, $headers, $requiredFields)
    {
        $this->headers = $headers;
        $this->data = $data;

        if(count($requiredFields) > 0){
            foreach($requiredFields as $field){
                $this->{$field} = $this->validateRequiredField($data, $field);
            }
        }
      
        foreach($this->headers as $key => $header){
            if(in_array($key, $requiredFields)){
               continue;
            }
            $this->{$key} = $data[$key] ?? '';
        }

    }

    public function setRequiredFields($requiredFields){
        $this->requiredFields = $requiredFields;
    }

    private function validateRequiredField($data, $fieldName)
    {
        if (empty($data[$fieldName])) {
            throw new Exception("Required field '$fieldName' not found in the data.");
        }
        return $data[$fieldName];
    }

    public function getProductArray(){
        $productArray = [];
        if(count($this->requiredFields) > 0){
            foreach($this->requiredFields as $key => $fieldName){
               $productArray[$fieldName] = $this->data[$fieldName] . " (" . gettype($this->data[$fieldName]) . ", required)" . " - " . ucwords(str_replace("_", " ", $this->headers[$fieldName]));
            }
        }
      
        foreach($this->headers as $key => $header){
            if(in_array($key, $this->requiredFields)){
               continue;
            }

            $productArray[$key]  = $this->data[$key] . " (" . gettype($this->data[$key]) . ")" . " - " . ucwords(str_replace("_", " ", $header));
        }
        return $productArray;
    }

    public function toArray()
    {
        return [
            'make' => $this->make,
            'model' => $this->model,
            'colour' => $this->colour,
            'capacity' => $this->capacity,
            'network' => $this->network,
            'grade' => $this->grade,
            'condition' => $this->condition,
        ];
    }
}