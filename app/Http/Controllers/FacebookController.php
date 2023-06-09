<?php

namespace App\Http\Controllers;

use App\Services\FacebookApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FacebookController extends Controller
{
    public function setWebhook(Request $request)
    {
        if ($request->hub_verify_token === config("facebook.verify_token")) {
            return $request->hub_challenge;
        }
    }

    public function webhook(Request $request, FacebookApiService $facebookApiService)
    {
        $data = $request->all();

        if ($data['object'] === 'page') {
            foreach ($data['entry'] as $entry) {
                foreach ($entry['changes'] as $change) {
                    if ($change['field'] === 'leadgen') {
                        $leadgenId = $change['value']['leadgen_id'];

                        Log::channel("facebook")->debug(print_r($facebookApiService->lead($leadgenId), true));
                    }
                }
            }
        }
    }
}
