<?php

namespace gateways\oauth2;

use gateways\AbstractGateway;
use gateways\GatewayInterface;

class ScopeGateway extends AbstractGateway implements GatewayInterface
{
	/**
	 * @inheritdoc
	 */
	public function getTable()
	{
		return 'oauth2_scopes';
	}
}