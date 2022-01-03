<?php

namespace Fypex\GamivoClient\Denormalizer\Offers;

use Fypex\GamivoClient\Models\Offer\OfferResponseModel;
use Fypex\GamivoClient\Models\Offer\SetExternalIdResponseModel;

class SetExternalIdDenormalizer
{

    public function denormalize($data): SetExternalIdResponseModel
    {

        return new SetExternalIdResponseModel($data);

    }

}
