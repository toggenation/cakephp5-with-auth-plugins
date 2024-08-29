<?php

namespace App\Identifier\Resolver;

use ArrayAccess;
use Authentication\Identifier\Resolver\OrmResolver;

class CaseInsensitiveOrmResolver extends OrmResolver
{
    public function find(array $conditions, string $type = self::TYPE_AND): ArrayAccess|array|null
    {
        $table = $this->getTableLocator()->get($this->_config['userModel']);

        $query = $table->selectQuery();
        $finders = (array)$this->_config['finder'];
        foreach ($finders as $finder => $options) {
            if (is_string($options)) {
                $query->find($options);
            } else {
                $query->find($finder, ...$options);
            }
        }

        $where = [];
        foreach ($conditions as $field => $value) {
            $field = $table->aliasField($field);
            if (is_array($value)) {
                $field = $field . ' IN';
            }
            $where[$field . ' LIKE'] = $value;
        }
        // dd($query->where([$type => $where])->__toString());
        return $query->where([$type => $where])->first();
    }
}
