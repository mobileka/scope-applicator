<?php namespace Stubs\Fake;

use Mobileka\MosaicArray\MosaicArray;

class DataProvider
{
    /**
     * @var array|\Mobileka\MosaicArray\MosaicArray
     */
    public $data = [
        'one' => ['val' => 1],
        'five' => ['val' => 5],
        'six' => ['val' => 6],
    ];

    public function __construct()
    {
        $this->data = new MosaicArray($this->data);
    }

    /**
     * A scope with an argument
     *
     * @param  string $one
     * @return static
     */
    public function one($one)
    {
        $this->data->replaceTarget($this->data->getItem($one, []));

        return $this;
    }

    /**
     * A scope without arguments
     *
     * @return static
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
     * @return static
     */
    public function six($six = 'six')
    {
        $this->data->replaceTarget($this->data->getItem($six, []));

        return $this;
    }

    /**
     * A scope with two arguments
     *
     * @param  mixed $min
     * @param  mixed $max
     * @return static
     */
    public function between($min, $max)
    {
        $result = [];

        foreach ($this->data as $key => $value) {
            $greaterThanMin = $value['val'] >= $min or $min === '';
            $lessThanMax = $value['val'] <= $max or $max === '';

            if ($greaterThanMin and $lessThanMax) {
                $result[$key] = ['val' => $value['val']];
            }
        }

        $this->data->replaceTarget($result);

        return $this;
    }

    /**
     * @return array
     */
    public function get()
    {
        return $this->data->toArray();
    }
}
