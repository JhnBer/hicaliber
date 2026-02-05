<?php

namespace App\Jobs;

use App\Events\ImportStatusChanged;
use App\Events\PropertyCountUpdated;
use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProcessExamplePropertyDataJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $uniqueFor = 60;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    public function uniqueId(): string
    {
        return 'seed_properties_lock';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Cache::set('properties_import', true, $this->uniqueFor);
        event(new ImportStatusChanged(true));

        sleep(5);

        try {
            DB::transaction(function () {
                $data = [
                    ['name' => 'The Victoria', 'price' => 374662, 'bedrooms' => 4, 'bathrooms' => 2, 'storeys' => 2, 'garages' => 2],
                    ['name' => 'The Xavier', 'price' => 513268, 'bedrooms' => 4, 'bathrooms' => 2, 'storeys' => 1, 'garages' => 2],
                    ['name' => 'The Como', 'price' => 454990, 'bedrooms' => 4, 'bathrooms' => 3, 'storeys' => 2, 'garages' => 3],
                    ['name' => 'The Aspen', 'price' => 384356, 'bedrooms' => 4, 'bathrooms' => 2, 'storeys' => 2, 'garages' => 2],
                    ['name' => 'The Lucretia', 'price' => 572002, 'bedrooms' => 4, 'bathrooms' => 3, 'storeys' => 2, 'garages' => 2],
                    ['name' => 'The Toorak', 'price' => 521951, 'bedrooms' => 5, 'bathrooms' => 2, 'storeys' => 1, 'garages' => 2],
                    ['name' => 'The Skyscape', 'price' => 263604, 'bedrooms' => 3, 'bathrooms' => 2, 'storeys' => 2, 'garages' => 2],
                    ['name' => 'The Clifton', 'price' => 386103, 'bedrooms' => 3, 'bathrooms' => 2, 'storeys' => 1, 'garages' => 1],
                    ['name' => 'The Geneva', 'price' => 390600, 'bedrooms' => 4, 'bathrooms' => 3, 'storeys' => 2, 'garages' => 2],
                ];

                foreach ($data as $item) {
                    Property::updateOrCreate(['name' => $item['name']], $item);
                }
            });

        } finally {
            Cache::forget('properties_import');
            event(new ImportStatusChanged(false));
            event(new PropertyCountUpdated(Property::query()->count()));
        }
    }
}
