<?php
namespace App\Signz\Model;
abstract class PointModel extends \Framework\Model\BaseModel {
	const TABLENAME = "points";
	const TABLE_COORDS_NAME = "coordinates";
	const LIMIT = 100;
	abstract protected function getType(): string;
	abstract protected function buildObject(\stdClass $Obj): \App\Signz\Record\Point;
	
	
	public function getById(int $id): \App\Signz\Record\Point {
		$id = intval($id);
		$key = __NAMESPACE__.'\\'.static::class.':ID_'.$id;
	
		$Point = $this->Memcached->get($key);
		if(!$Point || true) {
			try {
				$Stmt = $this->Database->prepare("SELECT * FROM ".self::TABLENAME." WHERE id = ? LIMIT 1", "i", array($id));
				if($Stmt->execute()) {
					$Obj = $Stmt->getNextRow($Stmt::OBJECT);
					if(!$Obj) {
						$Point = null;
					} else {
						$Point = $this->buildObject($Obj);
					}
					$this->Memcached->set($key,$Point);
				}
				$Stmt->cleanup();
			} catch(\Framework\Exception\NoQueryExecutedException $e) {
				$Point = null;
			} catch(\Framework\Exception\QueryExecutionException $e) {
				$Point = null;
			}
		}
	
		return $Point;
	}
	
	public function getByName(string $name): \App\Signz\Record\Point {
		$key = __NAMESPACE__.'\\'.static::class.':NAME_'.$name;
	
		$Point = null;
		$id = $this->Memcached->get($key);
		if(!$id || true) {
			try {
				$Stmt = $this->Database->prepare("SELECT id FROM ".self::TABLENAME." WHERE name = ? LIMIT 1", "s", array($key));
				if($Stmt->execute()) {
					$Obj = $Stmt->getNextRow($Stmt::OBJECT);
					if($Obj) {
						$Point = $this->getById($Obj->id);
					}
					$this->Memcached->set($key,$Point);
				}
				$Stmt->cleanup();
			} catch(\Framework\Exception\NoQueryExecutedException $e) {
				$Point = null;
			} catch(\Framework\Exception\QueryExecutionException $e) {
				$Point = null;
			}
		}
	
		return $Point;
	}
	
	public function getByType(string $type, int $offset = 0, int $limit = self::LIMIT): array {
		$key = __NAMESPACE__.'\\'.static::class.':TYPE_'.$type;
	
		$list = $this->Memcached->get($key);
		if(!is_arary($list) || empty(slice($list,$offset,$limit)) || true) {
			$list = array();
			try {
				$Stmt = $this->Database->prepare("SELECT * FROM ".self::TABLENAME." WHERE type = ? LIMIT ?, ?", "sii", array($type,$offset,$limit));
				if($Stmt->execute()) {
					while($Obj = $Stmt->getNextRow($Stmt::OBJECT)){
						$Point = null;
						if($Obj) {
							$Point = $this->buildObject($Obj);
						}
						$list[$offset] = $Point;
						$offset++;
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
	
	public function getCoords(int $id): array {
		$key = __NAMESPACE__.'\\'.static::class.':COORDS_'.$id;
		
		$points = $this->Memcached->get($key);
		if(!$points || empty($points) || true) {
			$points = array();
			try {
				$Stmt = $this->Database->prepare("SELECT * FROM ".self::TABLE_COORDS_NAME." WHERE point_id = ? ORDER BY `order` ASC", "i", array($id));
				if($Stmt->execute()) {
					$Obj = $Stmt->getNextRow($Stmt::OBJECT);
					while($Obj = $Stmt->getNextRow($Stmt::OBJECT)){
						if($Obj) {
							$points[] = $Obj;
						}
					};
					$this->Memcached->set($key,$points);
				}
				$Stmt->cleanup();
			} catch(\Framework\Exception\NoQueryExecutedException $e) {
				$Point = null;
			} catch(\Framework\Exception\QueryExecutionException $e) {
				$Point = null;
			}
		}
		
		return $points;
	}
	
	public function getByRadius(float $lat, float $long, float $radius, string $type = "", int $offset = 0, int $limit = self::LIMIT): array {
		$key = __NAMESPACE__.'\\'.static::class.':LOCATION_'.implode(":",array($lat,$long,$radius,$type));
		
		$list = $this->Memcached->get($key);
		if(!is_array($list) || empty(array_slice($list,$offset,$limit)) || true) {
			$list = array();
			try {
				$this->Database->reconnect();
				$Stmt = $this->Database->query("SELECT *, SQRT(POW(69.1 * (`latitude` - {$lat}), 2) + POW(69.1 *({$long} - `longitude`) * COS(`latitude` / 57.3), 2)) as distance FROM ".self::TABLENAME . " HAVING distance <= {$radius} ORDER BY distance ASC");
				
				if($Stmt->execute()) {
					$i = 0;
					while($Obj = $Stmt->getNextRow($Stmt::OBJECT)){
						$Point = null;
						if($Obj) {
							$Point = $this->buildObject($Obj);
							$Point->coords = self::getCoords($Point->id);
						}
						$list[$i] = $Point;
						$i++;
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
		
		$list = array_slice($list,$offset,$limit);
		return $list;
	}
}