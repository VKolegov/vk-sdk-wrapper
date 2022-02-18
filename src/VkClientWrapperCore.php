<?php
/*
 * @Author: Kolegov Vladislav
 * @Date: 2019-11-17 11:56:31
 * @Last Modified by: Kolegov Vladislav
 * @Last Modified time: 2020-01-03 02:39:14
 */

namespace VKolegov\VKAPIWrapper;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use VK\Client\VKApiClient;
use VK\Exceptions\VKApiException;
use VK\Exceptions\VKClientException;

class VkClientWrapperCore
{
    private string $access_token;

    private VKApiClient $client;

    public function __construct($access_token, $version = 5.94)
    {
        $this->access_token = $access_token;

        // TODO: Lang?
        $this->client = new VKApiClient($version);
    }

    public function call($method, $params)
    {
        try {
            $response = $this->client->getRequest()->post($method, $this->access_token, $params);
        } catch (VKApiException $vke) {

            $code = $vke->getCode();
            $message = $vke->getMessage();

            if ($code == 6 || $code == 9) {
                usleep(500000); // TODO: Убрать этот стремный костыль
                return $this->call($method, $params);
            } else {
                \Log::error("[VK] Метод: " . $method . " параметры: " . json_encode($params) . " Ошибка код: " . $code . " сообщение: " . $message);
                return $code;
            }
        }

        // \Log::debug($response);

        // TODO: после переезда с ATehnixVkClient никогда не произойдет
        if (isset($response['error'])) {
            \Log::error("[VK] Метод: " . $method . " параметры: " . json_encode($params) . " Ответ: " . $response);
            return $response['error'];
        }

        return $response;
    }

    private function arrayToVks($array)
    {
        if (Arr::isAssoc($array)) {
            return $this->assocArrayToVks($array);
        } else {
            return $this->seqArrayToVks($array);
        }
    }

    private function seqArrayToVks($array)
    {
        $vks_array = "[";

        foreach ($array as $k => $v) {

            if (is_array($v)) {
                $vks_array .= $this->arrayToVks($v) . ", ";
            } else {
                $vks_array .= $v . ", ";
            }

        }

        $vks_array .= "]";

        return $vks_array;
    }

    private function assocArrayToVks($array)
    {
        $vks_array = "{";

        foreach ($array as $k => $v) {
            $vks_array .= $k . ": ";

            if (is_array($v)) {
                $vks_array .= $this->arrayToVks($v) . ", ";
            } else {
                $vks_array .= $v . ", ";
            }

        }

        $vks_array .= "}";

        return $vks_array;
    }

    public function callScript($script_path, $script_params)
    {

        $script = file_get_contents($script_path);

        foreach ($script_params as $param_key => $param_value) {

            if (is_array($param_value)) {
                $vks_array = $this->arrayToVks($param_value);
                $script = str_replace("%" . strtoupper($param_key) . "%", $vks_array, $script);

                continue;
            }

            if (is_string($param_value)) {
                $script = str_replace("%" . strtoupper($param_key) . "%", '"' . $param_value . '"', $script);
                continue;
            }

            $script = str_replace("%" . strtoupper($param_key) . "%", $param_value, $script);
        }

        $params = [
            'code' => $script,
        ];

        $response = [];

        try {
            $response = $this->client->getRequest()->post('execute', $this->access_token, $params);
        } catch (VKApiException $e) {
            Log::error($e);
        } catch (VKClientException $e) {
            Log::error($e);
        }

        return $response;
    }
}
