<?php

namespace app\models\DTO;

use JsonSerializable;

class ItemDTO implements JsonSerializable {

    private $id;
    private $title;
    private $artistName;
    private $format;
    private $year;
    private $label;
    private $rating;
    private $comment;
    private $buyPrice;
    private $condition;

    public function __construct(
        $id, $title, $artistName, $format, $year, $label, $rating, $comment, 
        $buyPrice, $condition
    ) {

        $this->id = $id;
        $this->title = $title;
        $this->artistName = $artistName;
        $this->format = $format;
        $this->year = $year;
        $this->label = $label;
        $this->rating = $rating;
        $this->comment = $comment;
        $this->buyPrice = $buyPrice;
        $this->condition = $condition;

    }

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }



    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Get the value of title
     */
    public function getTitle()
    {
        return $this->title;
    }



    /**
     * Get the value of artist
     */
    public function getArtistName()
    {
        return $this->artistName;
    }



    /**
     * Get the value of format
     */
    public function getFormat()
    {
        return $this->format;
    }



    /**
     * Get the value of year
     */
    public function getYear()
    {
        return $this->year;
    }



    /**
     * Get the value of label
     */
    public function getLabel()
    {
        return $this->label;
    }


    /**
     * Get the value of rating
     */
    public function getRating()
    {
        return $this->rating;
    }



    /**
     * Get the value of comment
     */
    public function getComment()
    {
        return $this->comment;
    }



    /**
     * Get the value of buyPrice
     */
    public function getBuyPrice()
    {
        return $this->buyPrice;
    }


    /**
     * Get the value of condition
     */
    public function getCondition()
    {
        return $this->condition;
    }

}