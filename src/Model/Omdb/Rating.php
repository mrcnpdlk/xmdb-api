<?php
/**
 * Created by Marcin.
 * Date: 10.12.2017
 * Time: 00:27
 */

namespace mrcnpdlk\Xmdb\Model\Omdb;


class Rating
{
    /**
     * @var string
     */
    public $source;
    /**
     * @var string
     */
    public $value;

    /**
     * Rating constructor.
     *
     * @param string      $source
     * @param string|null $value
     */
    public function __construct(string $source, string $value = null)
    {
        $this->source = $source;
        $this->value  = $value;
    }
}
