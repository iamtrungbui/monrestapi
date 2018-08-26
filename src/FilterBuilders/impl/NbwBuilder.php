<?php
namespace Iamtrungbui\Monrestapi\FilterBuilders\Impl;

use Iamtrungbui\Monrestapi\FilterBuilders\FilterBuilder;

class NbwBuilder extends FilterBuilder
{
    const regex = '/(^[a-zA-Z0-9\.\_\-]+)\!\=\[([\d]+);([\d]+)\]/';
    protected $level = 6;
    public function buildQueryParam($filterParam)
    {
        preg_match(self::regex, $filterParam, $matches);
        if (count($matches) == 4) {
            return [
                "field" => $matches[1],
                "value" => [$matches[2], $matches[3]],
            ];
        } else {
            return false;
        }
    }
    public function buildQuery($query, $filter)
    {
        return $query->whereNotBetween($filter['field'], $filter['value']);
    }
}
