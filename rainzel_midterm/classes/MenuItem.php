<?php
class MenuItem {
    private $id;
    private $name;
    private $description;
    private $price;
    private $category;
    private $image;
    private $isAvailable;
    
    public function __construct($id, $name, $description, $price, $category, $image) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->category = $category;
        $this->image = $image;
        $this->isAvailable = true;
    }
    
    public function getPrice() {
        return $this->price;
    }
    
    public function isAvailable() {
        return $this->isAvailable;
    }
    
    public function setAvailability($status) {
        $this->isAvailable = $status;
    }
}
