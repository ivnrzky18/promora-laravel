<?php

namespace App\Console\Commands;

use App\Models\Promo;
use Illuminate\Console\Command;

class UpdateHotDeals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'promos:update-hot-deals';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark promos ending within 48 hours as hot deals';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $threshold = now()->addHours(48);

        // Set is_hot_deal = true for active promos ending within 48 hours
        Promo::where('status', 'active')
             ->where('end_date', '<=', $threshold)
             ->where('end_date', '>=', now())
             ->update(['is_hot_deal' => true]);

        // Reset is_hot_deal = false for promos no longer meeting criteria
        Promo::where('is_hot_deal', true)
             ->where(function ($q) use ($threshold) {
                 $q->where('status', '!=', 'active')
                   ->orWhere('end_date', '>', $threshold)
                   ->orWhere('end_date', '<', now());
             })
             ->update(['is_hot_deal' => false]);
    }
}
