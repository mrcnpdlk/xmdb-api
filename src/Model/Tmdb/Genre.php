<?php
/**
 * Created by Marcin.
 * Date: 30.11.2017
 * Time: 00:11
 */

namespace mrcnpdlk\Xmdb\Model\Tmdb;


class Genre
{
    /**
     * @var integer
     */
    public $id;
    /**
     * @var string
     */
    public $name;

    public function __construct(int $id, string $name)
    {
        $this->id   = $id;
        $this->name = $name;
    }
}
