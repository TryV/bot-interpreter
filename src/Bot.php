<?php

namespace Tryv\PhpJsonBotInterpreter;

use Exception;
use Tryv\PhpJsonBotInterpreter\Interface\ValueInterface;

class Bot
{
	private $curl_handle = null;

	/// constructor
	public function __construct(public string $token)
	{
		$this->curl_handle = curl_init('https://api.telegram.org/bot' . $this->token . '/');
		curl_setopt_array($this->curl_handle, [
			CURLOPT_HEADER				=> false,
			CURLOPT_RETURNTRANSFER		=> true,
			CURLOPT_SSL_VERIFYPEER		=> false,
			CURLOPT_POST				=> true,
            // CURLOPT_PROXY				=> '127.0.0.1:10809',
		]);
	}

	/// destruct
	public function __destruct()
	{
		curl_close($this->curl_handle);
	}

	/// calling tg methods
	public function __call(string $name, array $arguments)
	{
		if (array_is_list($arguments)===false) {
			$request = $this->sendAPIRequest($name, $arguments);
		}
		else {
			$request = $this->sendAPIRequest($name, $arguments[0] ?? []);
		}

		return $request;
		// if ($request['ok']===true)
			// return $request['result'] ?? $request['ok'];
	}

	/// prepare request data
	private function prepareData(array $data) : array
	{
		$data = array_filter($data, fn ($_) => $_!==null);

		array_walk($data, function (&$v){
			if ($v instanceof ValueInterface) $v = $v->getValue();
			if (is_array($v)===true || $v instanceof \stdClass) $v = json_encode($v);
		});

		return $data;
	}

    private function handleCurlError()
    {
        if (curl_errno($this->curl_handle)!==0) {
            throw new Exception(curl_error($this->curl_handle), curl_errno($this->curl_handle));
		}
    }

	/// Do requests to Telegram Bot API

	/**
	 * Contacts the various API's endpoints
	 * @param $api the API endpoint
	 * @param $content the request parameters as array
	 * @return bool|array the JSON Telegram's reply.
	 */
	public function sendAPIRequest(string $api, array $content = [])
	{
		$content = $this->prepareData($content);

		$content['method'] = $api;
		curl_setopt($this->curl_handle, CURLOPT_POSTFIELDS, $content);

		$result = curl_exec($this->curl_handle);
        $this->handleCurlError();

		if (curl_errno($this->curl_handle)===0) {
			$object = json_decode($result, true);

			if ($object['ok']===false)
				// logger('TG Error on ' . $api, [json_encode($content), json_encode($object, 128+256)]);

			return $object;
		}

		return ['ok' => false];
	}

    public function getFileUrl(string $file_id): ?string
    {
        $file_info = $this->getFile(file_id: $file_id);
        if ($file_info['ok'] === false) {
            return null;
        }

        return 'https://api.telegram.org/file/bot' . $this->token . '/' . $file_info['result']['file_path'];
    }
}


