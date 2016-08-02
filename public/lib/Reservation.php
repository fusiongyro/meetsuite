<?php

/**
 * Class Reservation
 *
 * The core of the reservation-acceptance/approval system.
 * Represents a reservation, either requested or approved and pending.
 */
class Reservation
{
    private $id;
    private $contact;
    private $room;
    private $state = ReservationState::UNSUBMITTED;
    private $isSigned;

    /**
     * The core state machine of the reservation; returns true only for
     * transitions that are allowed from the current state.
     *
     * @param $anotherState  ReservationState
     *   a possible state to transition to
     * @return bool
     *   true if the state transition is acceptable
     */
    private function canTransitionTo($anotherState)
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

    /**
     * Performs a state transition, throwing an exception if it is not allowed.
     *
     * @param $anotherState  ReservationState
     *   the state to attempt to transition to
     */
    private function transitionTo($anotherState)
    {
        if ($this->canTransitionTo($anotherState))
            $this->state = $anotherState;
        else
            throw new InvalidArgumentException("Can't transition from {$this->state} to $anotherState");
    }

    /**
     * Submits the reservation. The expectation is that this is performed
     * on behalf of an anonymous or semi-anonymous user.
     *
     * Side effects include sending mail and adding the reservation to the
     * Google calendar.
     */
    public function submit()
    {
        $this->validate();
        $this->transitionTo(ReservationState::AWAITING_APPROVAL);
        $this->addToCalendar();
    }

    /**
     * Denies this reservation request. Performed by library staff.
     *
     * Side effects include sending mail and removing occurrences from the
     * shared Google calendar.
     */
    public function deny()
    {
        $this->transitionTo(ReservationState::DENIED);
        $this->removeFromCalendar();
        $this->sendDenialMessage();
    }

    /**
     * Expires this reservation request. Performed automatically on a schedule.
     * The expectation is that a periodic task will notice that the start time
     * for this reservation has elapsed without a decision having been made
     * about whether it is to be approved or not.
     *
     * Side effects include sending mail and removing occurrences from the
     * shared Google calendar.
     */
    public function expire()
    {
        $this->transitionTo(ReservationState::EXPIRED);
        $this->sendExpirationMessage();
        $this->removeFromCalendar();
    }

    /**
     * Cancels a reservation request. Performed by the owner, this is how a
     * user withdraws a request.
     *
     * Side effects include sending mail and removing occurrences from the
     * shared Google calendar.
     */
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