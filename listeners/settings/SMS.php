<?php
namespace packages\melipayamak_smsgateway\listeners\settings;

use packages\base;
use packages\sms\events\Gateways;
use packages\melipayamak_smsgateway\API;

class SMS {
	public function gatewaysList(Gateways $gateways): void {
		$gateway = new Gateways\Gateway('melipayamak');
		$gateway->setHandler(API::class);
		$gateway->addInput(array(
			'name' => 'melipayamak_username',
			'type' => 'string'
		));
		$gateway->addInput(array(
			'name' => 'melipayamak_password',
			'type' => 'string'
		));
		$gateway->addField(array(
			'name' => 'melipayamak_username',
			'label' => t('settings.sms.gateways.melipayamak.username'),
			'ltr' => true
		));
		$gateway->addField(array(
			'type' => 'password',
			'name' => 'melipayamak_password',
			'label' => t('settings.sms.gateways.melipayamak.password'),
			'ltr' => true
		));
		$gateways->addGateway($gateway);
	}
}
