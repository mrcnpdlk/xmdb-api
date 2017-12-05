<?php
/**
 * Created by Marcin.
 * Date: 02.12.2017
 * Time: 00:10
 */

namespace mrcnpdlk\Xmdb\Model\Imdb;


class Title
{
    /**
     * @var string
     */
    public $title;
    /**
     * @var boolean
     */
    public $isMovie;
    /**
     * @var string
     */
    public $imdbId;
    /**
     * @var float
     */
    public $rating;
    /**
     * @var string
     */
    public $episode;
    /**
     * @var string
     */
    public $year;
    /**
     * @var string
     */
    public $type;
    /**
     * @var string[]
     */
    public $director;
    /**
     * @var string
     */
    public $directorDisplay;
    /**
     * @var string[]
     */
    public $star;
    /**
     * @var string
     */
    public $starDisplay;
}
