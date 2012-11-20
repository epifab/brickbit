<?php
namespace system\logic;

abstract class EditComponent extends Component {	
	abstract protected function getTemplateForm();
	abstract protected function getTemplateNotify();
	
	final protected function getTemplate() {
		if (!is_null(@$this->datamodel["private"]["responseType"]) && $this->datamodel["private"]["responseType"] == Component::RESPONSE_TYPE_NOTIFY) {
			return $this->getTemplateNotify();
		} else {
			return $this->getTemplateForm();
		}
	}
	
	public function loadData(\system\model\RecordsetInterface $recordset, &$errors, $editFieldPathList) {
		
		$numErrors = 0;
		$builder = $recordset->getBuilder();

		if (\array_key_exists("recordset", $_REQUEST) && \is_array($_REQUEST["recordset"])) {
			
			foreach ($editFieldPathList as $path) {
				$value = \array_key_exists($path, $_REQUEST["recordset"]) ? $_REQUEST["recordset"][$path] : null;
				
				$metaType = $builder->searchMetaType($path);
				if ($metaType == null) {
					throw new InternalErrorException("Path $path non valido");
				}

				try {
					if ($metaType instanceof MetaBoolean) {
						$value = \is_null($value) ? 0 : 1;
					}
					
					if ($metaType instanceof MetaDate || $metaType instanceof MetaTime || $metaType instanceof MetaDateTime) {
						$date = $metaType instanceof MetaDate || $metaType instanceof MetaDateTime;
						$time = $metaType instanceof MetaTime || $metaType instanceof MetaDateTime;

	//					if ($date) {
	//						$dateArr = @$value["date"];
	//						if (\is_null($dateArr)) {
	//							throw new ValidationException("Data non trasmessa");
	//						}
	//						if (!\is_array($dateArr) || !\array_key_exists("d", $dateArr) || !\array_key_exists("m", $dateArr) || !\array_key_exists("y", $dateArr)) {
	//							throw new ValidationException("Formato data non valido");
	//						}
	//						MetaInteger::formalValidation($dateArr["y"]);
	//						MetaInteger::formalValidation($dateArr["m"]);
	//						MetaInteger::formalValidation($dateArr["d"]);
	//						$y = $dateArr["y"];
	//						$m = $dateArr["m"];
	//						$d = $dateArr["d"];
	//					} else {
	//						$y = 0;
	//						$m = 0;
	//						$d = 0;
	//					}

						if ($date) {
							$dateArr = @$value["date"];
							if (!\array_key_exists("date", $value) || empty($value["date"])) {
								throw new ValidationException("Data non trasmessa");
							}
							list($d,$m,$y) = \explode("/", $dateArr);
						} else {
							$y = 0;
							$m = 0;
							$d = 0;
						}

						if ($time) {
							$timeArr = @$value["time"];
							if (\is_null($timeArr)) {
								throw new ValidationException("Ora non trasmessa");
							}
							if (!\is_array($timeArr) || !\array_key_exists("h", $timeArr) || !\array_key_exists("i", $timeArr) || !\array_key_exists("s", $timeArr)) {
								throw new ValidationException("Formato data non valido");
							}
							MetaInteger::formalValidation($timeArr["h"]);
							MetaInteger::formalValidation($timeArr["i"]);
							MetaInteger::formalValidation($timeArr["s"]);
							$h = $timeArr["h"];
							$i = $timeArr["i"];
							$s = $timeArr["s"];
						} else {
							$h = 0;
							$i = 0;
							$s = 0;
						}

						$progValue = \mktime($h,$i,$s,$m,$d,$y);

						if ($date && $time) {
							$value = date("d/m/Y H:i:s", $progValue);
						} else if ($date) {
							$value = date("d/m/Y", $progValue);
						} else {
							$value = date("H:i:s", $progValue);
						}
					}

					$curRecordset = $recordset;
					$curPath = $path;
				
					do {
						$dotPosition = strpos($curPath, ".");

						if ($dotPosition === false) {
							// Recordset target
							$curRecordset->setEdit($curPath, $value);
						} else {
							$relationName = substr($curPath, 0, $dotPosition);
							$curRecordset = $curRecordset->__get($relationName);
							$curPath = substr($curPath, $dotPosition+1);
						}
					} while ($dotPosition !== false);
					
				} catch (ValidationException $ex) {
					$errors[$path] = $ex->getMessage();
					$numErrors++;
				} catch (ConversionException $ex) {
					$errors[$path] = $ex->getMessage();
					$numErrors++;
				}
			}
			if ($numErrors == 0) {
				return true;
			}
		}
		return false;
	}
	
	public function checkKey(\system\model\RecordsetInterface $recordset, &$errors, $keyName) {
		$builder = $recordset->getBuilder();
		$key = $builder->getKey($keyName);
		if (is_null($key)) {
			throw new InternalErrorException("Chiave $keyName non trovata");
		}
		
		$newFilter = null;
		foreach ($key as $metaType) {
			$fieldValue = $recordset->getEdit($metaType->getName());
			$filterClause = new \system\model\FilterClause($metaType, "=", $fieldValue);
			if (is_null($newFilter)) {
				$newFilter = new \system\model\FilterClauseGroup($filterClause);
			} else {
				$newFilter->addClauses("AND", $filterClause);
			}
		}
		
		if ($recordset->isStored()) {
			// taglio il record corrispondente a quello che sto modificando
			$primary = $builder->getPrimaryKey();
			foreach ($primary as $metaType) {
				$fieldValue = $recordset->getEdit($metaType->getName());
				$filterClaue = new \system\model\FilterClause($metaType, "<>", $fieldValue);
				$newFilter->addClauses("AND", $filterClaue);
			}
		}
		
		$oldFilter = $builder->getFilter();
		$builder->setFilter($newFilter);
		$numRecords = $builder->countRecords(true);
		$builder->setFilter($oldFilter);
		
		if ($numRecords == 0) {
			return true;
		} else {
			foreach ($key as $metaType) {
				$errors[$metaType->getAbsolutePath()] = "Chiave duplicata";
			}
			return false;
		}
	}
	
	public function checkHasOneRelation(\system\model\RecordsetInterface $recordset, &$errors, $relationName, $required=true) {
		$builder = $recordset->getBuilder();
		$relationBuilder = $builder->searchRelationBuilder($relationName);
		if (\is_null($relationBuilder)) {
			throw new InternalErrorException("Relazione $relationName inesistente");
		}
		
		$newFilter = null;
		$nullRelation = false;
		try {
			foreach ($relationBuilder->getClauses() as $parentFieldName => $childFieldName) {
				$fieldValue = $recordset->getEdit($parentFieldName);

				if (\is_null($fieldValue)) {
					if ($required || !\is_null($newFilter)) {
						// due possibilita':
						// 1 la relazione e' obbligatoria
						// 2 ci sono campi del join che sono stati specificati mentre questo e' nullo
						throw new InternalErrorException("Alcuni campi della relazione $relationName sono nulli");
					} else {
						// relazione non obbligatoria e campo (fin'ora) tutti nulli
						$nullRelation = true;
					}
				} else {
					if ($nullRelation) {
						// i campi fin'ora erano tutti nulli
						throw new InternalErrorException("Alcuni campi della relazione $relationName sono nulli");
					}
				}

				$metaType = $relationBuilder->searchMetaType($childFieldName);
				$filterClause = new \system\model\FilterClause($metaType, "=", $fieldValue);
				if (\is_null($newFilter)) {
					$newFilter = new \system\model\FilterClauseGroup($filterClause);
				} else {
					$newFilter->addClauses("AND", $filterClause);
				}
			}
			
			if (\is_null($newFilter)) {
				return true;
			}
		
			$oldFilter = $relationBuilder->getFilter();
			$relationBuilder->setFilter($newFilter);
			$numRecords = $relationBuilder->countRecords(true);
			$relationBuilder->setFilter($oldFilter);

			if ($numRecords == 0) {
				throw new ValidationException("I campi non identificano alcuna relazione");
			}
			
			return true;
			
		} catch (ValidationException $ex) {
			foreach ($relationBuilder->getClauses() as $parentField => $childField) {
				$errors[$builder->searchMetaType($parentField)->getAbsolutePath()] = $ex->getMessage();
			}
			return false;
		}
	}
}
?>