<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TransferSuppliesToStorage extends Command
{
    protected $signature = 'transfer:supplies';
    protected $description = 'Transfer supplies from supply_detail to storage';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $supplies = DB::table('supply_detail')
            ->join('supplies_status', 'supply_detail.id_supply', '=', 'supplies_status.id_supply')
            ->where('supplies_status.is_added', false)
            ->select('supply_detail.*')
            ->get();

        foreach ($supplies as $supply) {
            $existingProduct = DB::table('storage')->where('id_product', $supply->id_product)->first();

            if ($existingProduct) {
                DB::table('storage')
                    ->where('id_product', $supply->id_product)
                    ->update(['quantity_products' => $existingProduct->quantity_products + $supply->quantity]);
            } else {
                DB::table('storage')->insert([
                    'id_product' => $supply->id_product,
                    'quantity_products' => $supply->quantity,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        DB::table('supplies_status')
            ->whereIn('id_supply', $supplies->pluck('id_supply'))
            ->update(['is_added' => true]);

        $this->info('Supplies have been transferred to storage successfully.');
    }
}
