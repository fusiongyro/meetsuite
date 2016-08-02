<?php

class Occurrence
{
    private $state = OccurrenceState::PENDING;

    public function canTransitionTo($anotherState)
    {
        switch ($this->state) {
            case OccurrenceState::PENDING:
                return $anotherState == OccurrenceState::IN_PROGRESS;

            case ReservationState::IN_PROGRESS:
                return $anotherState == OccurrenceState::COMPLETE;
        }

        return false;
    }

    public function transitionTo($anotherState)
    {
        if ($this->canTransitionTo($anotherState))
            $this->state = $anotherState;
        else
            throw new InvalidArgumentException("Can't transition from {$this->state} to $anotherState");
    }
}