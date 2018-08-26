<?php
namespace Iamtrungbui\Monrestapi\FilterBuilders\Impl;

use Iamtrungbui\Monrestapi\FilterBuilders\FilterBuilder;

class InBuilder extends FilterBuilder
{
    const regex = '/(^[a-zA-Z0-9\.\_\-]+)\=\{(.+)\}/';
    protected $level = 9;
    public function buildQueryParam($filterParam)
    {
        preg_match(self::regex, $filterParam, $matches);
        if (count($matches) == 3) {
            return [
                "field" => $matches[1],
                "value" => explode(';', $matches[2]),
            ];
        } else {
            return false;
        }
    }
    public function buildQuery($query, $filter)
    {
        return $query->whereIn($filter['field'], $filter['value']);
    }
}
