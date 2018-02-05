<?php

namespace Sandbox\Models;

use Fluxoft\Rebar\Db\Model;

/**
 * Class Type
 * @package Sandbox\Models
 * @property int ID
 * @property string Name
 */
class Type extends Model {
	protected $propertyDbMap = [
		'ID' => 'id',
		'Name' => 'name'
	];

	protected $dbTable = 'types';
}
