<?php

namespace app\models\entity;

class ExternalIdEntity {
    
    private $id;
    private $supplier;
    private $value;
    private $itemid;

    public function __construct(
        int $id, string $supplier, string $value, string $itemid
    ) {

        $this->id = $id;
        $this->supplier = $supplier;
        $this->value = $value;
        $this->itemid = $itemid;
    }



    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     */
    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of supplier
     */
    public function getSupplier()
    {
        return $this->supplier;
    }

    /**
     * Set the value of supplier
     */
    public function setSupplier($supplier): self
    {
        $this->supplier = $supplier;

        return $this;
    }

    /**
     * Get the value of value
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set the value of value
     */
    public function setValue($value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get the value of itemid
     */
    public function getItemid()
    {
        return $this->itemid;
    }

    /**
     * Set the value of itemid
     */
    public function setItemid($itemid): self
    {
        $this->itemid = $itemid;

        return $this;
    }
}