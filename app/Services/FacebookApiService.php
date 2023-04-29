<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class FacebookApiService
{
    const URL      = "https://graph.facebook.com/";
    const FULL_URL = "https://graph.facebook.com/v16.0/";

    private string $appId;
    private string $appSecret;
    private string $userShortAccessToken;
    private string $token;

    public function __construct()
    {
        $this->appId                = config("facebook.app_id");
        $this->appSecret            = config("facebook.app_secret");
        $this->userShortAccessToken = config("facebook.user_short_access_token");
        $this->token                = DB::table("options")->where("key", "accessToken")->first()->value;
    }

    public function generateToken(): void
    {
        $access_token = Http::get(self::URL . "oauth/access_token?client_id=$this->appId&client_secret=$this->appSecret&grant_type=fb_exchange_token&fb_exchange_token&fb_exchange_token=$this->userShortAccessToken")
            ->json()["access_token"];

        DB::table("options")->where("key", "accessToken")->updateOrInsert(
            ["key"   => "accessToken"],
            ["value" => $access_token]);
    }

    public function lead($leadId)
    {
        $response = Http::withToken($this->token)
            ->get(self::FULL_URL . $leadId)
            ->json();

        if (isset($response["error"]) && $response["error"]["code"] === 190) {
            $this->generateToken();

            $token = DB::table("options")->where("key", "accessToken")->first()->value;

            return Http::withToken($token)
                ->get(self::FULL_URL . $leadId)
                ->json();
        }

        return $response;
    }
}
