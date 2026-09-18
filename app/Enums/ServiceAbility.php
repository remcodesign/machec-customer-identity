<?php

namespace App\Enums;

/**
 * This app's own local list of Sanctum M2M abilities it can issue (D101) —
 * a plain backed enum, never `Machec\Contracts\Enums\RoleName` (which is
 * for human roles) and never shared with `pim-core`'s own, non-overlapping
 * `ServiceAbility` list.
 */
enum ServiceAbility: string
{
    case IdentityAddressesRead = 'identity:addresses:read';
    case IdentityCustomersRead = 'identity:customers:read';
}
