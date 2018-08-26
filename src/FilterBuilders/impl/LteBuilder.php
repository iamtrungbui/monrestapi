<?php
namespace Iamtrungbui\Monrestapi\FilterBuilders\Impl;

use Iamtrungbui\Monrestapi\FilterBuilders\FilterBuilder;

class LteBuilder extends FilterBuilder
{
    const regex = '/(^[a-zA-Z0-9\.\_\-]+)\<\=(.*)/';
    protected $level = 2;
    public function buildQueryParam($filterParam)
    {
        preg_match(self::regex, $filterParam, $matches);
        if (count($matches) == 3) {
            return [
                "field" => $matches[1],
                "value" => $matches[2],
            ];
        } else {
            return false;
        }
    }
    public function buildQuery($query, $filter)
    {
        return $query->where($filter['field'], '<=', $filter['value']);
    }
}
