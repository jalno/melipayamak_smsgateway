<?php
namespace packages\melipayamak_smsgateway;

use packages\base\{Exception, Http, Json};
use packages\sms\{Sent, Gateway};
use function packages\base\utility\getTelephoneWithDialingCode;

class API extends Gateway\Handler {

	/** @var string */
	private $username;

	/** @var string */
	private $password;

	public function __construct(Gateway $gateway) {
		$this->username = $gateway->param('melipayamak_username');
		$this->password = $gateway->param('melipayamak_password');
		if (!$this->username or !$this->password) {
			throw new Exception('Credential is missing');
		}
	}

	/**
	 * Send the sms
	 * 
	 * @param Sent $sms
	 * @return int new status of sms
	 */
	public function send(Sent $sms): int {
		$http = new http\Client();
		try {
			$response = $http->post('https://rest.payamak-panel.com/api/SendSMS/SendSMS', array(
				'form_params' => array(
					'UserName' => $this->username,
					'PassWord' => $this->password,
					'From' => $sms->sender_number->number,
					'To' => $this->convertReceiverNumber($sms->receiver_number),
					'Text' => $sms->text,
					'IsFlash' => false,
				),
			));
			$body = $response->getBody();
			if ($body) {
				try {
					$decoded = Json\decode($body);
					if ((isset($decoded['RetStatus']) and $decoded['RetStatus']) or
						(isset($decoded['StrRetStatus']) and strtolower($decoded['StrRetStatus']) == 'ok')) {
						return Sent::sent;
					}
				} catch (Json\JsonException $e) {}
			}
		} catch (\Exception $e) {}
		return Sent::failed;
	}
	/**
     * @param string $number that is something like this: IR.9387654321
     *
     * @return string that is converted to this: 98.9387654321
     */
    protected function convertReceiverNumber(string $number): string
    {
        return getTelephoneWithDialingCode($number);
    }

}
