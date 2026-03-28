<?php
// app/Console/Commands/SyncBnmExchangeRates.php

namespace App\Console\Commands;

use App\Models\Currency;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SyncBnmExchangeRates extends Command
{
    protected $signature = 'bnm:sync-exchange-rates';
    protected $description = 'Sync latest exchange rates from Bank Negara Malaysia (BNM)';

    public function handle(): int
    {
        $url = 'https://api.bnm.gov.my/public/exchange-rate';

        $res = Http::withHeaders([
                'Accept' => 'application/vnd.BNM.API.v1+json',
            ])
            ->timeout(30)
            ->get($url);

        if (!$res->ok()) {
            $this->error("BNM request failed: HTTP {$res->status()}");
            $this->line('Response: ' . substr((string) $res->body(), 0, 500));
            return self::FAILURE;
        }

        $json = $res->json();

        $rows = data_get($json, 'data', []);
        $meta = data_get($json, 'meta', []);

        if (!is_array($rows) || count($rows) === 0) {
            $this->error('BNM response had no data rows.');
            return self::FAILURE;
        }

        $quote       = data_get($meta, 'quote');              // "rm"
        $session     = data_get($meta, 'session');            // "1700"
        $lastUpdated = data_get($meta, 'last_updated');       // "YYYY-MM-DD HH:MM:SS"
        $rateDate    = data_get($rows, '0.rate.date');

        /**
         * Base currency (MYR) must always be active + default.
         * We keep forcing this because it is your system base currency.
         */
        $myr = Currency::firstOrCreate(
            ['code' => 'MYR'],
            [
                'name'       => 'MYR',
                'symbol'     => 'RM',
                'unit'       => 1,
                'is_active'  => true,
                'is_default' => true,
            ]
        );

        $myr->fill([
            'rate_date'       => $rateDate,
            'buying_rate'     => 1.00000000,
            'selling_rate'    => 1.00000000,
            'middle_rate'     => 1.00000000,
            'source'          => 'BNM',
            'quote'           => $quote,
            'session'         => $session,
            'last_updated_at' => $lastUpdated,
        ]);

        // ensure MYR always stays active/default even if edited
        $myr->is_active  = true;
        $myr->is_default = true;
        $myr->save();

        $count = 0;

        foreach ($rows as $row) {
            $code = data_get($row, 'currency_code');
            if (!$code || strtoupper($code) === 'MYR') {
                continue;
            }

            $unit    = (int) (data_get($row, 'unit', 1) ?: 1);
            $date    = data_get($row, 'rate.date');
            $buying  = data_get($row, 'rate.buying_rate');
            $selling = data_get($row, 'rate.selling_rate');
            $middle  = data_get($row, 'rate.middle_rate');

            // Create currency if missing (FIRST sync behavior)
            $currency = Currency::firstOrCreate(
                ['code' => $code],
                [
                    // default values ONLY on first create
                    'name'      => $code,
                    'unit'      => $unit,
                    'is_active' => false,
                ]
            );

            // Keep existing name if admin customized it
            if (empty($currency->name)) {
                $currency->name = $code;
            }

            // Always update rate fields (safe to overwrite)
            $currency->fill([
                'unit'            => $unit,
                'rate_date'       => $date,
                'buying_rate'     => is_null($buying) ? null : (float) $buying,
                'selling_rate'    => is_null($selling) ? null : (float) $selling,
                'middle_rate'     => is_null($middle) ? null : (float) $middle,
                'source'          => 'BNM',
                'quote'           => $quote,
                'session'         => $session,
                'last_updated_at' => $lastUpdated,
            ]);

            $currency->save();

            $count++;
        }

        $this->info("Synced {$count} currencies from BNM.");
        return self::SUCCESS;
    }
}
