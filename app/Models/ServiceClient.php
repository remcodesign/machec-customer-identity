<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;

/**
 * A machine credential (D99) — deliberately not a `users` row, so it never
 * shows up in `UserIndex`/`UserShow`/`UserCreate` (Step 2.9), never holds a
 * `RoleName`, and is never subject to those screens' self/admin-protection
 * guardrails, which were designed around human accounts only.
 *
 * @property int $id
 * @property string $label
 * @property Carbon|null $created_at
 * @property Carbon|null $deleted_at
 */
#[Fillable(['label'])]
class ServiceClient extends Model
{
    use HasApiTokens, SoftDeletes;

    const UPDATED_AT = null;
}
