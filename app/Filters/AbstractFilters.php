<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 12/6/18
 * Time: 5:14 PM
 */

namespace App\Filters;

use Illuminate\Http\Request;

abstract class AbstractFilters
{

    protected $request;
    protected $builder;
    protected $filters = [];

    /**
     * ThreadFilter constructor.
     * @param $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Apply filter if applicable
     *
     * @param $builder
     * @return mixed
     */
    public function apply($builder)
    {
        $this->builder= $builder;

        foreach($this->getFilters() as $filter => $value) {
            if( method_exists($this, $filter)) {
                $this->$filter($value);
            }
        }
        return $this->builder;
    }

    /**
     * @return array
     */
    private function getFilters(): array
    {
        return $this->request->only($this->filters);
    }

}