<?php

namespace Webgk\Helper;

class Curl
{
	private $url;
	private $curl;
	private $response;
	private $availableStatuses = [200];
	private $logFile = false;

	public function __construct($url, $isPost = false, $data = [])
	{
		$this->url = $url;
		$this->curl = curl_init();

		$this->setUrl($this->url);
		$this->setReturnTransfer(true);

		$this->setPost($isPost);

		if (!empty($data)) {
			$this->setData($data);
		}
	}

	public function setOption($option, $value)
	{
		curl_setopt($this->curl, $option, $value);
	}

	public function setOptions($options)
	{
		curl_setopt_array($this->curl, $options);
	}

	public function setHeaders($headers)
	{
		$this->setOption(CURLOPT_HTTPHEADER, $headers);
	}

	public function setTimeout($timeout)
	{
		$this->setOption(CURLOPT_TIMEOUT, (int)$timeout);
	}

	public function setUrl($url)
	{
		$this->setOption(CURLOPT_URL, $url);
	}

	public function setPost($value)
	{
		$this->setOption(CURLOPT_POST, $value);
	}

	public function setData($data, $jsonEncode = false)
	{
		$this->setOption(CURLOPT_POSTFIELDS, $jsonEncode ? json_encode($data) : $data);
	}

	public function setReturnTransfer($value)
	{
		$this->setOption(CURLOPT_RETURNTRANSFER, $value);
	}

	public function setAvailableReturnStatuses($statuses)
	{
		$this->availableStatuses = $statuses;
	}

	public function setFileToLogExceptions($fileName)
	{
		$this->logFile = $fileName;
	}

	/**
	 * @throws \Exception
	 */
	public function exec()
	{
		$this->response = curl_exec($this->curl);
		$code = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);

		if (!in_array($code, $this->availableStatuses)) {
			if ($this->logFile) {
				Logger::log(['code' => $code, 'response' => $this->response], $this->logFile);
			}

			throw new \Exception(curl_error($this->curl));
		}

		curl_close($this->curl);
	}

	public function getResponse()
	{
		if (!$this->response) {
			$this->exec();
		}

		return $this->response;
	}

	public function getJsonResponse()
	{
		return json_decode($this->getResponse(), true);
	}

	public function getJsonResponseNotAssoc()
	{
		return json_decode($this->getResponse());
	}
}
