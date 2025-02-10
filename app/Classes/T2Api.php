<?php

namespace App\Classes;

use App\Exceptions\T2ApiException;
use App\Models\NumberStat;
use App\Models\Option;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class T2Api
{
    protected $accessToken;
    protected $refreshToken;
    protected $managerNumbers;

    public function __construct()
    {
        $this->refreshToken = Option::where('name', 't2_refresh_token')->first() ?? null;
        $access_token = Option::where('name', 't2_access_token')->first() ?? null;

        if (!$access_token) {
            $this->getNewTokens();
            return;
        }

        $this->accessToken = $access_token->value;
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

    public static $realManagersNumbers = [
        79922896554,
        79535174387,
        79922802826,
        79005325832,
        79922857462,
        79951460603,
        79922893028,
        79922889975,
        79922833926,
        79954943528,
        79922871233,
        79535175470,
        79922865076,
        79922802313,
        79535174769,
        79922883450,
        79922806049,
        79535175529,
        79005479481,
        79922851746,
    ];

    public function getDataFromT2Api($dateStart, $dateEnd)
    {
        if (!$this->accessToken) {
            $this->getNewTokens();
        }

        $date_start = urlencode("{$dateStart}T00:00:01+03:00");
        $date_end = urlencode("{$dateEnd}T23:59:59+03:00");

        $size = 3000;

        $target_url = "https://ats2.t2.ru/crm/openapi/call-records/info?start={$date_start}&end={$date_end}&size={$size}";

        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => $this->accessToken,
            'Content-Type' => 'application/json',
        ])->get($target_url);

        $info = $response->headers();
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

    public function importDataFromApi($dateStart, $dateEnd)
    {
        $data = $this->getDataFromT2Api($dateStart, $dateEnd);

        $calculatedData = $this->calculateManagerCallsData($data);

        $savingCount = 0;

        foreach ($calculatedData as $key => $number_data) {
            $save_result = $this->saveNumberStat($number_data);
            if ($save_result) {
                $savingCount++;
            }
        }

        return $savingCount;
    }

    public static function saveNumberStat(array $numberData): bool
    {
        if (empty($numberData)) {
            return false;
        }
    
        $record = NumberStat::updateOrCreate(
            [
                'number' => $numberData['number'],
                'date' => $numberData['date'],
            ],
            $numberData
        );
    
        return $record !== null;
    }

    public function calculateManagerCallsData($data)
    {
        $resultArray = [];
        $tempArray = [];

        foreach ($data as $dataObject) {
            // Если длительность меньше 10 сек, пропуск
            if (!isset($dataObject['conversationDuration']) || $dataObject['conversationDuration'] < 10) {
                continue;
            }

            $date = Carbon::parse($dataObject['date'])->format('Y-m-d');

            // Если менеджер ответил
            if (in_array($dataObject['calleeNumber'], self::$realManagersNumbers)) {
                $managerNumber = $dataObject['calleeNumber'];
                if (!isset($tempArray[$managerNumber])) {
                    $tempArray[$managerNumber] = [];
                }
                if (!isset($tempArray[$managerNumber][$date])) {
                    $tempArray[$managerNumber][$date] = [
                        'income' => 0,
                        'outcome' => 0,
                        'duration' => 0,
                    ];
                }
                $tempArray[$managerNumber][$date]['income']++;
                $tempArray[$managerNumber][$date]['duration'] += $dataObject['conversationDuration'];
                continue;
            }

            // Если менеджер звонил
            if (in_array($dataObject['callerNumber'], self::$realManagersNumbers)) {
                $managerNumber = $dataObject['callerNumber'];
                if (!isset($tempArray[$managerNumber])) {
                    $tempArray[$managerNumber] = [];
                }
                if (!isset($tempArray[$managerNumber][$date])) {
                    $tempArray[$managerNumber][$date] = [
                        'income' => 0,
                        'outcome' => 0,
                        'duration' => 0,
                    ];
                }
                $tempArray[$managerNumber][$date]['outcome']++;
                $tempArray[$managerNumber][$date]['duration'] += $dataObject['conversationDuration'];
                continue;
            }
        }

        foreach ($tempArray as $managerNumber => $dates) {
            foreach ($dates as $date => $stats) {
                $resultArray[] = [
                    'number' => $managerNumber,
                    'date' => $date,
                    'income' => $stats['income'],
                    'outcome' => $stats['outcome'],
                    'duration' => $stats['duration'],
                ];
            }
        }

        return $resultArray;
    }
}
