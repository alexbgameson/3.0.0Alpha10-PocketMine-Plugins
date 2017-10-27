<?php
namespace rankup\rank;

class Rank{
    private $name;
    private $price;
    private $order;

    public function __construct($name, $order, $price){
        $this->name = $name;
        $this->order = $order;
        $this->price = $price;
    }

    /**
     * @param mixed $name
     */
    public function setName($name){
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName(){
        return $this->name;
    }

    /**
     * @param mixed $order
     */
    public function setOrder($order){
        $this->order = $order;
    }

    /**
     * @return mixed
     */
    public function getOrder(){
        return $this->order;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price){
        $this->price = $price;
    }

    /**
     * @return mixed
     */
    public function getPrice(){
        return $this->price;
    }

}