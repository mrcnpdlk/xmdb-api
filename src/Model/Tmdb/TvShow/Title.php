<?php
/**
 * Created by Marcin.
 * Date: 29.11.2017
 * Time: 22:02
 */

namespace mrcnpdlk\Xmdb\Model\Tmdb\TvShow;


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
    public $inProduction;
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
    public $firstAirDate;
    /**
     * @var string|null
     */
    public $lastAirDate;
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
    public $productionCompanies = [];
    /**
     * @var string[]
     */
    public $originCountries = [];
    /**
     * @var array
     */
    public $createdBy = [];
    /**
     * @var integer[]
     */
    public $episodeRuntimes;
    /**
     * @var string[]
     */
    public $languages = [];
    /**
     * @var \mrcnpdlk\Xmdb\Model\Tmdb\TvShow\Network[]
     */
    public $networks = [];
    /**
     * @var string
     */
    public $episodesNumber;
    /**
     * @var integer
     */
    public $seasonsNumber;
    /**
     * @var \mrcnpdlk\Xmdb\Model\Tmdb\TvShow\Season[]
     */
    public $seasons;
    /**
     * @var string
     */
    public $status;
    /**
     * @var string
     */
    public $type;
    /**
     * @var string
     */
    public $tagline;

}
