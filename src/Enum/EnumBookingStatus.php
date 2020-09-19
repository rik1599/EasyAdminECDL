<?php

namespace App\Enum;

abstract class EnumBookingStatus extends Enum
{
    const SUBSCRIBED = 'SUBSCRIBED';
    const CANCELED = 'CANCELED';
    const SESSION_CANCELED = 'SESSION_CANCELED'; 
}