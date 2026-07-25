<?php

namespace App\Events;

use App\Models\SupplierProfile;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SupplierInventorySynced
{
    use Dispatchable, SerializesModels;

    public function __construct(public SupplierProfile $supplier, public int $updated) {}
}
