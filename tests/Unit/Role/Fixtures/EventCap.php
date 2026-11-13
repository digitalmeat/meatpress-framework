<?php

declare(strict_types=1);

namespace Tests\Unit\Role\Fixtures;

use Meatpress\Attributes\CapabilitySet;

#[CapabilitySet(label: 'Events')]
enum EventCap: string
{
    case ExportAttendees = 'export_attendees';
    case ScanTickets = 'scan_tickets';
}
