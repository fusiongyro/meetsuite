<?php

class OccurrenceState extends SplEnum
{
    const __default   = ReservationState::PENDING;
    const PENDING     = "PENDING";
    const IN_PROGRESS = "IN_PROGRESS";
    const COMPLETE    = "COMPLETE";
}