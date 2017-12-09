<?php
/**
 * Created by Marcin.
 * Date: 09.12.2017
 * Time: 23:15
 */

namespace mrcnpdlk\Xmdb\Model\Omdb;


use Carbon\Carbon;

class Title
{
    /**
     * @var string
     */
    public $title;
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
    public $year;
    /**
     * @var
     */
    public $rated;
    /**
     * @var string
     */
    public $released;
    /**
     * @var string
     */
    public $runtime;
    /**
     * @var string[]
     */
    public $genre = [];
    /**
     * @var string[]
     */
    public $director = [];
    /**
     * @var string[]
     */
    public $writer = [];
    /**
     * @var string[]
     */
    public $actors = [];
    /**
     * @var string
     */
    public $plot;
    /**
     * @var string
     */
    public $language;
    /**
     * @var string
     */
    public $country;
    /**
     * @var string
     */
    public $awards;
    /**
     * @var string
     */
    public $poster;
    /**
     * @var \mrcnpdlk\Xmdb\Model\Omdb\Rating[]
     */
    public $ratings = [];
    /**
     * @var int|null
     */
    public $metascore;
    /**
     * @var string
     */
    public $type;
    /**
     * @var integer
     */
    public $totalSeasons;
    /**
     * @var string | null
     */
    public $dvd;
    /**
     * @var string | null
     */
    public $boxOffice;
    /**
     * @var string | null
     */
    public $production;
    /**
     * @var string | null
     */
    public $website;

    public static function create(\stdClass $oData)
    {
        $oTitle               = new static();
        $oTitle->title        = $oData->Title;
        $oTitle->year         = $oData->Year;
        $oTitle->released     = Carbon::parse($oData->Released)->format('Y-m-d');
        $oTitle->runtime      = $oData->Runtime;
        $oTitle->genre        = explode(', ', $oData->Genre);
        $oTitle->director     = $oData->Director === 'N/A' ? null : explode(', ', $oData->Director);
        $oTitle->writer       = explode(', ', $oData->Writer);
        $oTitle->actors       = explode(', ', $oData->Actors);
        $oTitle->plot         = $oData->Plot;
        $oTitle->language     = $oData->Language;
        $oTitle->country      = $oData->Country;
        $oTitle->awards       = $oData->Awards;
        $oTitle->poster       = $oData->Poster;
        $oTitle->metascore    = $oData->Metascore === 'N/A' ? null : (int)$oData->Metascore;
        $oTitle->imdbRating   = (float)$oData->imdbRating;
        $oTitle->imdbVotes    = (int)preg_replace('/,/', '', $oData->imdbVotes);
        $oTitle->imdbId       = $oData->imdbID;
        $oTitle->type         = $oData->Type;
        $oTitle->totalSeasons = !isset($oData->totalSeasons) || $oData->totalSeasons === 'N/A' ? null : (int)$oData->totalSeasons;
        $oTitle->dvd          = isset($oData->DVD) ? Carbon::parse($oData->DVD)->format('Y-m-d') : null;
        $oTitle->boxOffice    = $oData->BoxOffice ?? null;
        $oTitle->production   = $oData->Production ?? null;
        $oTitle->website      = $oData->Website ?? null;

        foreach ($oData->Ratings as $rating) {
            $oTitle->ratings[] = new Rating($rating->Source, $rating->Value);
        }

        return $oTitle;
    }
}
