<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RecalculateProductRatings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:recalculate-ratings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate rating_average and review_count for all products';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Recalculating product ratings...');

        $products = \App\Models\Product::all();
        $bar = $this->output->createProgressBar(count($products));
        $bar->start();

        foreach ($products as $product) {
            $product->updateRating();
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Product ratings recalculated successfully!');

        return Command::SUCCESS;
    }
}
