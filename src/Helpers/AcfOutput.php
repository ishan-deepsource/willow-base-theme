<?php


namespace Bonnier\Willow\Base\Helpers;


class AcfOutput
{
    protected $acfArray;

    /**
     * constructor.
     *
     * @param mixed    $acfArray
     */
    public function __construct($acfArray)
    {
        $this->acfArray = $acfArray;
    }

    public function getString(string $key, string $default = null) : string {
        $get = array_get($this->acfArray, $key, $default);
        if (!is_string($get)) { return $default;}
        return $get;
    }
}