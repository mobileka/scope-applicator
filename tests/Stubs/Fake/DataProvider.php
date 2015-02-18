<?php namespace Stubs\Fake;

use Mobileka\MosaicArray\MosaicArray;

class DataProvider
{
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
     * @param  string             $one
     * @return Current_Class_Name
     */
    public function one($one)
    {
        $this->data->replaceTarget($this->data->getItem($one, []));

        return $this;
    }

    /**
     * A scope without arguments
     *
     * @return Current_Class_Name
     */
    public function five()
    {
        $this->data->replaceTarget($this->data->getItem('five', []));

        return $this;
    }

    /**
     * A scope with an optional argument
     *
     * @param  string             $six
     * @return Current_Class_Name
     */
    public function six($six = 'six')
    {
        $this->data->replaceTarget($this->data->getItem($six, []));

        return $this;
    }

    /**
     * A scope with two arguments
     *
     * @param  mixed              $min
     * @param  mixed              $max
     * @return Current_Class_Name
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

    public function get()
    {
        return $this->data->toArray();
    }
}
