<?php

namespace Bonnier\Willow\Base\Exceptions\Database;

class DuplicateEntryException extends \Exception
{
    private $data;

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param $data
     * @return DuplicateEntryException
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }
}
