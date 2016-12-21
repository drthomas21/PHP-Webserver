<?php
namespace App\Signz\Model;
class NewsModel {
  const TABLENAME = "news";
	const LIMIT = 10;
	protected function buildObject(\stdClass $Obj): \App\Signz\Record\News {
    return new \App\Signz\Record\News($obj);
  }

	public function getById(int $id): \App\Signz\Record\News {
		$id = intval($id);
		$key = __NAMESPACE__.'\\'.static::class.':ID_'.$id;

		$News = $this->Memcached->get($key);
		if(!$News || true) {
			try {
				$Stmt = $this->Database->prepare("SELECT * FROM ".self::TABLENAME." WHERE id = ? LIMIT 1", "i", array($id));
				if($Stmt->execute()) {
					$Obj = $Stmt->getNextRow($Stmt::OBJECT);
					if(!$Obj) {
						$News = null;
					} else {
						$News = $this->buildObject($Obj);
					}
					$this->Memcached->set($key,$News);
				}
				$Stmt->cleanup();
			} catch(\Framework\Exception\NoQueryExecutedException $e) {
				$News = null;
			} catch(\Framework\Exception\QueryExecutionException $e) {
				$News = null;
			}
		}

		return $News;
	}

	public function getAll(int $offset = 0, int $limit = self::LIMIT): array {
		$key = __NAMESPACE__.'\\'.static::class.':ALL';

		$list = $this->Memcached->get($key);
		if(!is_array($list) || empty(array_slice($list,$offset,$limit))) {
			$list = array();
			try {
				$this->Database->reconnect();
				$Stmt = $this->Database->query("SELECT * FROM ".self::TABLENAME." ORDER BY timestamp DESC");

				if($Stmt->execute()) {
					$i = 0;
					while($Obj = $Stmt->getNextRow($Stmt::OBJECT)){
						$News = null;
						if($Obj) {
							$News = $this->buildObject($Obj);
						}
						$list[$i] = $News;
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
