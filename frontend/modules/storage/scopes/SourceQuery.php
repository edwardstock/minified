<?php
/**
 * minified. 2014
 * @author Eduard Maksimovich <edward.vstock@gmail.com>
 *
 * Class: SourceQuery
 */

namespace frontend\modules\storage\scopes;


use yii\db\ActiveQuery;

class SourceQuery extends ActiveQuery{

	public function lastVersions() {
		$this->where = '`version` = (SELECT MAX(`version`) FROM `source` `sq` WHERE `sq`.`publicToken` = `publicToken`)';

		$this->groupBy('publicToken');

		return $this;
	}
}