<?php

namespace App\Observers;

use App\Models\Figurinha;

class FigurinhaObserver
{
    /**
     * Handle the Figurinha "creating" event.
     */
    public function creating(Figurinha $figurinha): void
    {
        if (empty($figurinha->slug)) {
            $baseSlug = \Illuminate\Support\Str::slug($figurinha->titulo);
            $slug = $baseSlug;
            $count = 1;
            
            while (Figurinha::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $count;
                $count++;
            }
            
            $figurinha->slug = $slug;
        }
    }

    /**
     * Handle the Figurinha "created" event.
     */
    public function created(Figurinha $figurinha): void
    {
        \Illuminate\Support\Facades\Log::info("Nova figurinha enviada: {$figurinha->titulo} ({$figurinha->slug})");
    }

    /**
     * Handle the Figurinha "updated" event.
     */
    public function updated(Figurinha $figurinha): void
    {
        //
    }

    /**
     * Handle the Figurinha "deleted" event.
     */
    public function deleted(Figurinha $figurinha): void
    {
        //
    }

    /**
     * Handle the Figurinha "restored" event.
     */
    public function restored(Figurinha $figurinha): void
    {
        //
    }

    /**
     * Handle the Figurinha "force deleted" event.
     */
    public function forceDeleted(Figurinha $figurinha): void
    {
        //
    }
}
