<?php
 
namespace App\Classes;

use App\Models\Option;
use Exception;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class T2Api
{
    protected $accessToken;
    protected $refreshToken;
    
    public function __construct()
    {
        $this->refreshToken = Option::whereName('t2_refresh_token')->first() ?? null;
        $accessToken = Option::whereName('t2_access_token')->first() ?? null;

        if (!$accessToken) {
            $this->getNewTokens();
            return;
        }

        $this->accessToken = $accessToken->value;
    }

    public function getDataFromT2Api($dateStart, $dateEnd)
    {
        if (!$this->accessToken) {
            $this->getNewTokens();
        }

        $date_start = urlencode("{$dateStart}T00:00:01+03:00");
        $date_end = urlencode("{$dateEnd}T23:59:59+03:00");

        $size = 2000;

        $target_url = "https://ats2.t2.ru/crm/openapi/call-records/info?start={$date_start}&end={$date_end}&size={$size}";

        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => $this->accessToken,
            'Content-Type' => 'application/json',
        ])->get($target_url);

        $responseData = $response->json();

        if ($response->status() == Response::HTTP_OK) {
            return $responseData;
        }

        if ($response->status() == Response::HTTP_FORBIDDEN) {
            if ($responseData['details'] == 'The token is expired') {
                $this->getNewTokens();
            }
            if ($responseData['details'] == 'The token has already been updated') {
                $this->sendError('Слишком устаревшие токены API! Обновите токены API вручную из кабинета T2 и сохраните в настройках!');
            }
        }

        $this->sendError('Не удалось получить данные. Неверный токен.');

        return [];
    }

    protected function getNewTokens()
    {
        if (!$this->refreshToken || $this->refreshToken == '') {
            $this->sendError('Не удалось получить REFRESH TOKEN из настроек, пожалуйста, обратитесь к разработчику!');
        }

        $targetUrl = "https://ats2.t2.ru/crm/openapi/authorization/refresh/token";
        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => $this->refreshToken->value,
            'Content-Type' => 'application/json',
        ])->put($targetUrl);

        $responseData = $response->json();

        if ($response->status() == 200) {
            $this->saveAccessToken($responseData['accessToken']);
            $this->saveRefreshToken($responseData['refreshToken']);

            return;
        }

        if (array_key_exists('details', $responseData) && $responseData['details'] == 'The token has already been updated') {
            $this->sendError('Слишком устаревшие токены API! Обновите токены API вручную из кабинета T2 и сохраните в настройках!');
        }

        $this->sendError('Не удалось обновить токены!');
    }

    protected function sendError(string $error)
    {
        throw new Exception($error);
    }

    protected function saveAccessToken(string $token)
    {
        Option::updateOrCreate(
            ['name' => 't2_access_token'],
            ['value' => $token]
        );
        $this->accessToken = $token;
    }

    protected function saveRefreshToken(string $token)
    {
        Option::updateOrCreate(
            ['name' => 't2_refresh_token'],
            ['value' => $token]
        );
        $this->refreshToken = $token;
    }
}
