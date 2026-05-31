<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ExpireVipBoosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vip:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove VIP status from groups whose boost has expired';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredGroups = \App\Models\Group::where('is_vip', true)
            ->whereNotNull('vip_expires_at')
            ->where('vip_expires_at', '<=', now())
            ->get();

        $count = 0;
        foreach ($expiredGroups as $group) {
            $group->update([
                'is_vip' => false,
            ]);
            $count++;
        }

        $this->info("Successfully expired VIP status for {$count} groups.");
    }
}
