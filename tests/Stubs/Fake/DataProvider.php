<?php namespace Stubs\Fake;

use Mobileka\MosaiqHelpers\MosaiqArray;

class DataProvider
{
    public $data = [
        'one' => ['val' => 1],
        'five' => ['val' => 5],
        'six' => ['val' => 6],
    ];

    public function __construct()
    {
        $this->data = new MosaiqArray($this->data);
    }

    /**
     * A scope with an argument
     *
     * @param  string $one
     * @return array
     */
    public function one($one)
    {
        $this->data->replaceTarget($this->data->getItem($one, []));

        return $this;
    }

    /**
     * A scope without arguments
     *
     * @return array
     */
    public function five()
    {
        $this->data->replaceTarget($this->data->getItem('five', []));

        return $this;
    }

    /**
     * A scope with an optional argument
     *
     * @param  string $six
     * @return array
     */
    public function six($six = 'six')
    {
        $this->data->replaceTarget($this->data->getItem($six, []));

        return $this;
    }

    public function between($min, $max)
    {
        $result = [];

        foreach ($this->data as $key => $value) {
            if ($value['val'] >= $min and $value['val'] <= $max) {
                $result[$key] = ['val' => $value];
            }
        }

        $this->data->replaceTarget($result);

        return $this;
    }

    public function get()
    {
        return $this->data->toArray();
    }
}
