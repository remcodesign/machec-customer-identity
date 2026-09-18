<?php

namespace App\Livewire;

use App\Models\Address;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard')]
class AdminDashboard extends Component
{
    #[Computed]
    public function totalUsers(): int
    {
        return User::count();
    }

    #[Computed]
    public function newRegistrations(): int
    {
        return User::where('created_at', '>=', now()->subDays(7))->count();
    }

    #[Computed]
    public function totalAddresses(): int
    {
        return Address::count();
    }

    /**
     * @return Collection<int, AuditLog>
     */
    #[Computed]
    public function recentAuditLog(): Collection
    {
        return AuditLog::with('user')->latest('created_at')->limit(5)->get();
    }

    public function render(): View
    {
        return view('dashboard');
    }
}
