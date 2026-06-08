<?php

namespace App\Console\Commands;

use App\Models\Promo;
use Illuminate\Console\Command;

class ExpirePromos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'promos:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set status=expired for promos past their end_date';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Promo::where('status', 'active')
             ->where('end_date', '<', now()->toDateString())
             ->update(['status' => 'expired', 'is_hot_deal' => false]);
    }
}
