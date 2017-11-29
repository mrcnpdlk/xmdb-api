<?php
/**
 * Created by Marcin.
 * Date: 30.11.2017
 * Time: 00:10
 */

namespace mrcnpdlk\Xmdb\Model\Tmdb\Movie;


class Collection
{
    /**
     * @var integer
     */
    public $id;
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $poster;
    /**
     * @var string
     */
    public $backdrop;

    public function __construct(int $id, string $name, string $poster = null, string $backdrop = null)
    {
        $this->id       = $id;
        $this->name     = $name;
        $this->poster   = $poster;
        $this->backdrop = $backdrop;
    }
}
