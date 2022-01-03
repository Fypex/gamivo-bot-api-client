<?php

namespace Fypex\GamivoClient\Models\Offer;

class SetExternalIdResponseModel
{

    private $message;

    public function __construct(array $data)
    {

        $this->message = $data['message'];

    }

    public function getMessage(): string
    {
        return $this->message;
    }

}
