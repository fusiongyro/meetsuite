<?php

abstract class OccurrenceState
{
    const __default   = ReservationState::PENDING;
    const PENDING     = "PENDING";
    const IN_PROGRESS = "IN_PROGRESS";
    const COMPLETE    = "COMPLETE";
}