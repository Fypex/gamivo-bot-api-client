<?php

namespace Fypex\GamivoClient\Models\Offer\Product;

class ProductResponseModel
{


    private $product_id;
    private $name;
    private $slug;
    private $platform;
    private $region;

    public function __construct($offer)
    {

        $this->product_id = $offer['product_id'];
        $this->name = $offer['$name'];
        $this->slug = $offer['$slug'];
        $this->platform = $offer['platform'];
        $this->region = $offer['region'];

    }

    public function getProductId()
    {
        return $this->product_id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function getPlatform()
    {
        return $this->platform;
    }

    public function getRegion()
    {
        return $this->region;
    }




}
