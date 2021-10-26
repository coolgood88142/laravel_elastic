<?php

namespace App\Http\Controllers;

use App\Services\ElasticService;
use Illuminate\Http\Request;

class TestController extends Controller
{
    protected $elasticService;

    public function __construct(ElasticService $elasticService)
    {
        $this->elasticService = $elasticService;
    }


    public function testElastic()
    {
        $client = $this->elasticService->addElastic(2, 'Ming');
		print_r('已建立成功!');
    }
}
