<?php

namespace saleemepoch\txtNation;

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use saleemepoch\txtNation\Exceptions\SMSMessageException;

abstract class Request
{
	const GATEWAY_API_ENDPOINT = 'http://client.txtnation.com/gateway.php';

	const DEFAULT_LOG_LOCATION = '../logs/messages.txt';
	const DEFAULT_MAX_LOG_FILES = 7;

	protected $objLogger = null;

	abstract protected function validate();

	public function startLogging()
	{
		$log_path = storage_path() . '/logs/sms/messages.txt';
		$this->objLogger->pushHandler(new RotatingFileHandler($log_path, self::DEFAULT_MAX_LOG_FILES, Logger::WARNING));
	}

	public function send()
	{
		if (!$this->validate()) {
			throw new SMSMessageException('Could not send message');
		}

		if (empty($this->strId)) {
			$objUuid = Uuid::uuid4();
			$this->strId = $objUuid->toString();
		}

		$arrParams = [
			'cc' => $this->strUsername,
			'ekey' => $this->strPassword,
			'message' => $this->strBody,
			'title' => $this->strSenderId,
			'network' => $this->strNetwork,
			'value' => $this->fltValue,
			'currency' => $this->strCurrency,
			'encoding' => $this->strEncoding,
			'number' => $this->strMsisdn,
			'id' => $this->strId,
			'reply' => $this->intReply
		];

		if ($this->blBinary) {
			$arrParams['binary'] = (int) $this->blBinary;
			$arrParams['udh'] = $this->strUdh;
		}

		if (!empty($this->shortcode))
			$arrParams['shortcode'] = $this->shortcode;

		$this->objLogger->addDebug('Sending the following to txtNation:', $arrParams);

		$objClient = new Client([
			'base_uri' => 'http://client.txtnation.com/', 'timeout'  => 10.0,
		]);

		$objResponse = $objClient->get('/gateway.php', [
			RequestOptions::QUERY => $arrParams,
			RequestOptions::SYNCHRONOUS => true,
			RequestOptions::ALLOW_REDIRECTS => true,
			RequestOptions::HEADERS => [
				'User-agent' => 'txtNationGatewayLibraryPHP/1.0'
			],
			RequestOptions::HTTP_ERRORS => false
		]);

		$objResult = new SMSMessageResult($objResponse);
		$objResult->setCallbackId($this->strId);

		if (!$objResult->success()) {
			$this->objLogger->addAlert('Message was not sent. ', ['error' => $objResult->getErrorMessage()]);
		}

		return $objResult;
	}
}
