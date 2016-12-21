<?php
namespace App\Signz\Model;
class GangModel extends PointModel {
	const LIMIT = 10;

	protected function getType(): string {
		$obj = new \App\Signz\Record\Gang();
		return $obj->type;
	}
	protected function buildObject(\stdClass $Obj): \App\Signz\Record\Point {
		return new \App\Signz\Record\Gang($Obj);
	}
	
}