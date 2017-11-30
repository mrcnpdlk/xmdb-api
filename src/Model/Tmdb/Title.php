<?php
/**
 * Created by Marcin.
 * Date: 29.11.2017
 * Time: 22:02
 */

namespace mrcnpdlk\Xmdb\Model\Tmdb;


class Title
{
    /**
     * @var integer
     */
    public $id;
    /**
     * @var boolean
     */
    public $isMovie;
    /**
     * @var string
     */
    public $title;
    /**
     * @var string
     */
    public $titleOrg;
    /**
     * @var string
     */
    public $titleOrgLang;
    /**
     * @var boolean
     */
    public $isAdult;
    /**
     * @var string
     */
    public $imdbId;
    /**
     * @var string|null
     */
    public $backdrop;
    /**
     * @var string|null
     */
    public $poster;
    /**
     * @var string|null
     */
    public $releaseDate;
    /**
     * @var string
     */
    public $releaseYear;
    /**
     * @var float
     */
    public $rating;
    /**
     * @var integer
     */
    public $voteCount;
    /**
     * @var float
     */
    public $popularity;
    /**
     * @var string
     */
    public $overview;
}