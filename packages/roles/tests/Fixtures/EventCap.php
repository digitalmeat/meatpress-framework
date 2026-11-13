<?php

declare(strict_types=1);

namespace Meatpress\Role\Tests\Fixtures;

use Meatpress\Attributes\CapabilitySet;

#[CapabilitySet(label: 'Events')]
enum EventCap: string
{
    case ExportAttendees = 'export_attendees';
    case ScanTickets = 'scan_tickets';
}
