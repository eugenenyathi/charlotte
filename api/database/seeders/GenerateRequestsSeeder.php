<?php

namespace Database\Seeders;

use App\Http\Controllers\GenerateRequestsController;
use App\Http\Services\GenerateRequestsService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GenerateRequestsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $requestsService = new GenerateRequestsService();
        $generateRequests = new GenerateRequestsController($requestsService);

        $generateRequests->init();
    }
}
