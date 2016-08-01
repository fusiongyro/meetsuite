<?php

class Reservation
{
    private $state = ReservationState::UNSUBMITTED;

    public function canTransitionTo($anotherState)
    {
        switch ($this->state) {
            case ReservationState::UNSUBMITTED:
                return $anotherState == ReservationState::AWAITING_APPROVAL;

            case ReservationState::AWAITING_APPROVAL:
                return in_array($anotherState,
                    array(ReservationState::ACCEPTED,
                        ReservationState::DENIED,
                        ReservationState::EXPIRED));

            case ReservationState::ACCEPTED:
                return in_array($anotherState,
                    array(ReservationState::CANCELED,
                        ReservationState::COMPLETE));
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

    public function submit()
    {
        $this->validate();
        $this->transitionTo(ReservationState::AWAITING_APPROVAL);
        $this->addToCalendar();
    }

    public function deny()
    {
        $this->transitionTo(ReservationState::DENIED);
        $this->removeFromCalendar();
        $this->sendDenialMessage();
    }

    public function expire()
    {
        $this->transitionTo(ReservationState::EXPIRED);
        $this->sendExpirationMessage();
        $this->removeFromCalendar();
    }

    public function cancel()
    {
        $this->transitionTo(ReservationState::CANCELED);
        $this->sendExpirationMessage();
        $this->removeFromCalendar();

    }
    public function approve()
    {
        $this->transitionTo(ReservationState::PENDING);
        $this->sendApprovalMessage();
    }

    public function save($repository)
    {
        $repository->saveReservation($this);
        $repository->saveOccurrences($this->getOccurrences());
    }

    public function getOccurrences()
    {

    }
}