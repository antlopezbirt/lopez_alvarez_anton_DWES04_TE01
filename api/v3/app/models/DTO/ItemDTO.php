<?php

namespace app\models\DTO;

use JsonSerializable;

class ItemDTO implements JsonSerializable {

    private $id;
    private $title;
    private $artist;
    private $format;
    private $year;
    private $origYear;
    private $label;
    private $rating;
    private $comment;
    private $buyPrice;
    private $condition;
    private $sellPrice;
    private $externalIds;

    public function __construct(
        $id, $title, $artist, $format, $year, $origYear, $label, $rating, $comment, 
        $buyPrice, $condition, $sellPrice, $externalIds = []
    ) {

        $this->id = $id;
        $this->title = $title;
        $this->artist = $artist;
        $this->format = $format;
        $this->year = $year;
        $this->year = $origYear;
        $this->label = $label;
        $this->rating = $rating;
        $this->comment = $comment;
        $this->buyPrice = $buyPrice;
        $this->condition = $condition;
        $this->sellPrice = $sellPrice;
        $this->externalIds = $externalIds;
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
    public function getArtist()
    {
        return $this->artist;
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


    /**
     * Get the value of externalIds
     */
    public function getExternalIds()
    {
        return $this->externalIds;
    }


    /**
     * Get the value of origYear
     */
    public function getOrigYear()
    {
        return $this->origYear;
    }

    /**
     * Get the value of sellPrice
     */
    public function getSellPrice()
    {
        return $this->sellPrice;
    }
}