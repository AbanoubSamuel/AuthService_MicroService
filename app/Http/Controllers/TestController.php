<?php

namespace App\Http\Controllers;

use App\Jobs\TestJob;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;

class TestController extends Controller {
    use Dispatchable;

    public function test(Request $request): void
    {
        $data = [
            'name' => 'Jon Doe',
            'phone' => '12345678901'
        ];
        TestJob::dispatch(new TestJob($data));
    }
}
