<?php
/**
 * Created by Marcin.
 * Date: 29.11.2017
 * Time: 22:02
 */

namespace mrcnpdlk\Xmdb\Model\Tmdb\Movie;


class Title
{
    /**
     * @var integer
     */
    public $id;
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
    public $isVideo;
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

    /**
     * @var \mrcnpdlk\Xmdb\Model\Tmdb\Movie\Collection|null
     */
    public $collection;
    /**
     * @var \mrcnpdlk\Xmdb\Model\Tmdb\Genre[]
     */
    public $genres = [];
    /**
     * @var
     */
    public $homepage;
    /**
     * @var \mrcnpdlk\Xmdb\Model\Tmdb\Company[]
     */
    public $productionCompanies =[];
    /**
     * @var \mrcnpdlk\Xmdb\Model\Tmdb\Country[]
     */
    public $productionCountries =[];
    /**
     * @var integer
     */
    public $budget;
    /**
     * @var integer
     */
    public $revenue;
    /**
     * @var integer
     */
    public $runtime;
    /**
     * @var \mrcnpdlk\Xmdb\Model\Tmdb\Language[]
     */
    public $spokenLanguages=[];
    /**
     * @var string
     */
    public $status;
    /**
     * @var string
     */
    public $tagline;

}
