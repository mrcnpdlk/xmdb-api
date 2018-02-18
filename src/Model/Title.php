<?php
/**
 * Created by Marcin.
 * Date: 18.02.2018
 * Time: 19:27
 */

namespace mrcnpdlk\Xmdb\Model;


class Title
{
    /**
     * @var string
     */
    public $imdbId;
    /**
     * @var float|null
     */
    public $imdbRating;
    /**
     * @var int|null
     */
    public $imdbVotes;
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
    public $releaseYear;
    /**
     * @var string|null
     */
    public $releaseDate;
    /**
     * @var string
     */
    public $runtime;
    /**
     * @var string[]
     */
    public $genres = [];
    /**
     * @var string
     */
    public $genresDisplay = '';
    /**
     * @var string[]
     */
    public $directors = [];
    /**
     * @var string
     */
    public $directorsDisplay = '';
    /**
     * @var string[]
     */
    public $writers = [];
    /**
     * @var string
     */
    public $writersDisplay = '';
    /**
     * @var string[]
     */
    public $actors = [];
    /**
     * @var string
     */
    public $actorsDisplay = '';
    /**
     * @var string
     */
    public $plot;
    /**
     * @var string
     */
    public $language;
    /**
     * @var string[]
     */
    public $countries = [];
    /**
     * @var string
     */
    public $countriesDisplay = '';
}
