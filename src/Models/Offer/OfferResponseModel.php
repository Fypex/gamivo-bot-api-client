<?php

namespace Fypex\GamivoClient\Models\Offer;

use Fypex\GamivoClient\Models\Offer\Product\ProductResponseModel;

class OfferResponseModel
{

    private $offer_id;
    private $product_id;
    private $product;
    private $created_at;
    private $updated_at;

    public function __construct($offer)
    {

        $this->offer_id = $offer['offer_id'];
        $this->product_id = $offer['product_id'];
        $this->product = new ProductResponseModel($offer['product']);
        $this->created_at = $offer['created_at'];
        $this->updated_at = $offer['updated_at'];

    }

    public function getOfferId()
    {
        return $this->offer_id;
    }

    public function getProductId()
    {
        return $this->product_id;
    }

    public function getProduct(): ProductResponseModel
    {
        return $this->product;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function getUpdatedAt()
    {
        return $this->updated_at;
    }


}
