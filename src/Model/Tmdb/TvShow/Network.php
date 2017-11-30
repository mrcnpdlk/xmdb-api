<?php

namespace mrcnpdlk\Xmdb\Model\Tmdb\TvShow;


class Network
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
     * Network constructor.
     *
     * @param int    $id
     * @param string $name
     */
    public function __construct(int $id, string $name)
    {
        $this->id   = $id;
        $this->name = $name;
    }
}