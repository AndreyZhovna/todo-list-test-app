<?php

use App\Domain\Shared\Helpers\ApiResponse;

function responder()
{
    return app(ApiResponse::class);
}


