<?php

abstract class ReservationState
{
    const __default = ReservationState::UNSUBMITTED;
    const UNSUBMITTED       = "UNSUBMITTED";
    const AWAITING_APPROVAL = "AWAITING_APPROVAL";
    const ACCEPTED          = "ACCEPTED";
    const COMPLETE          = "COMPLETE";
    const CANCELED          = "CANCELED";
    const DENIED            = "DENIED";
    const EXPIRED           = "EXPIRED";
}
