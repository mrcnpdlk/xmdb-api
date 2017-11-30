<?php

namespace mrcnpdlk\Xmdb\Model\Tmdb\TvShow;


class Season
{
    /**
     * @var integer
     */
    public $id;
    /**
     * @var integer
     */
    public $seasonNumber;
    /**
     * @var integer|null
     */
    public $episodeCount;
    /**
     * @var string|null
     */
    public $airDate;
    /**
     * @var  string|null
     */
    public $poster;

    /**
     * Season constructor.
     *
     * @param int         $id
     * @param int         $seasonNumber
     * @param int|null    $episodeCount
     * @param string|null $airDate
     * @param string|null $poster
     */
    public function __construct(int $id, int $seasonNumber, int $episodeCount = null, string $airDate = null, string $poster = null)
    {
        $this->id           = $id;
        $this->seasonNumber = $seasonNumber;
        $this->episodeCount = $episodeCount;
        $this->airDate      = $airDate;
        $this->poster       = $poster;
    }
}