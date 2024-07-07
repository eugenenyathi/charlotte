<?php

namespace App\Constants;


class RequestStatus
{
    const SUCCESS = "success";
    const FAILED = "failed";

    const ALLOCATED = "allocated";
    const ROOMS_ASSIGNED = "roomsAssigned";
    const PORTAL_CLOSED = "portalClosed";
    const NOT_REGISTERED = "notRegistered";

    const REQUESTER = "requester";
    const SELECTED = "selected";
    const NOT_SELECTED = "clean";

    const CONFIRMED = "confirmed";
    const WAITING = "waiting";
    const CANCELLED = "cancelled";
}
