<?php
namespace App\Signz\Model;
class GridModel extends \Framework\Model\BaseModel {
	const TABLENAME = "grid";
	const LIMIT = 10;
	protected function buildObject(\stdClass $Obj): \App\Signz\Record\Grid {
		return new \App\Signz\Record\Grid($Obj);
	}
	
	public function getByRadius(float $lat, float $long, float $radius): array {
		$key = __NAMESPACE__.'\\'.static::class.':LOCATION_'.implode(":",array($lat,$long,$radius));
		
		$list = $this->Memcached->get($key);
		if(!is_array($list) || true) {
			$list = array();
			try {
				$this->Database->reconnect();
				$Stmt = $this->Database->query("SELECT *, SQRT(POW(69.1 * (`latitude` - {$lat}), 2) + POW(69.1 *({$long} - `longitude`) * COS(`latitude` / 57.3), 2)) as distance FROM ".self::TABLENAME . " HAVING distance <= {$radius} ORDER BY distance ASC");
				
				if($Stmt->execute()) {
					while($Obj = $Stmt->getNextRow($Stmt::OBJECT)){
						$Point = null;
						if($Obj) {
							$Point = $this->buildObject($Obj);
						}
						$list[] = $Point;
					};
					
					$this->Memcached->set($key,$list);
				}
				$Stmt->cleanup();
			} catch(\Framework\Exception\NoQueryExecutedException $e) {
				// Do nothing
			} catch(\Framework\Exception\QueryExecutionException $e) {
				// Do nothing
			}
		}
		
		return $list;
	}
}