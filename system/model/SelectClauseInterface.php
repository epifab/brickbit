<?php
namespace system\model;

/**
 * Interfaccia per le clausole nelle select di recordsets
 * @author Fabio Epifani
 */
interface SelectClauseInterface extends \Serializable {
	public function getQuery();
}
