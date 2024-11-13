<?php
abstract class DeliveryMode {
    protected $orderId;
    protected $baseRate;
    protected $estimatedTime;
    
    public function __construct($orderId) {
        $this->orderId = $orderId;
    }
    
    abstract public function calculateDeliveryFee(): float;
    abstract public function getEstimatedTime(): string;
}

class StandardDelivery extends DeliveryMode {
    public function __construct($orderId) {
        parent::__construct($orderId);
        $this->baseRate = 49.00;
        $this->estimatedTime = "45-60 mins";
    }
    
    public function calculateDeliveryFee(): float {
        return $this->baseRate;
    }
    
    public function getEstimatedTime(): string {
        return $this->estimatedTime;
    }
}

class ExpressDelivery extends DeliveryMode {
    public function __construct($orderId) {
        parent::__construct($orderId);
        $this->baseRate = 99.00;
        $this->estimatedTime = "20-30 mins";
    }
    
    public function calculateDeliveryFee(): float {
        return $this->baseRate;
    }
    
    public function getEstimatedTime(): string {
        return $this->estimatedTime;
    }
}
