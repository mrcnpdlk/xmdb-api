<?php
/**
 * Created by Marcin.
 * Date: 09.12.2017
 * Time: 23:15
 */

namespace mrcnpdlk\Xmdb\Model\Omdb;


use Carbon\Carbon;

class Title extends \mrcnpdlk\Xmdb\Model\Title
{
    /**
     * @var
     */
    public $rated;
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

    /**
     * @param \stdClass $oData
     *
     * @return static
     */
    public static function create(\stdClass $oData)
    {
        $oTitle                   = new static();
        $oTitle->title            = $oData->Title;
        $oTitle->titleOrg         = $oData->Title;
        $oTitle->releaseYear      = $oData->Year;
        $oTitle->releaseDate      = $oData->Released !== 'N/A' ? Carbon::parse($oData->Released)->format('Y-m-d') : null;
        $oTitle->runtime          = (int)$oData->Runtime;
        $oTitle->genres           = explode(', ', $oData->Genre);
        $oTitle->directors        = $oData->Director === 'N/A' ? [] : explode(', ', $oData->Director);
        $oTitle->writers          = explode(', ', $oData->Writer);
        $oTitle->actors           = explode(', ', $oData->Actors);
        $oTitle->plot             = $oData->Plot;
        $oTitle->language         = $oData->Language;
        $oTitle->countriesDisplay = $oData->Country;
        $oTitle->awards           = $oData->Awards;
        $oTitle->poster           = $oData->Poster;
        $oTitle->metascore        = $oData->Metascore === 'N/A' ? null : (int)$oData->Metascore;
        $oTitle->imdbRating       = (float)$oData->imdbRating;
        $oTitle->imdbVotes        = (int)preg_replace('/,/', '', $oData->imdbVotes);
        $oTitle->imdbId           = $oData->imdbID;
        $oTitle->type             = $oData->Type;
        $oTitle->totalSeasons     = !isset($oData->totalSeasons) || $oData->totalSeasons === 'N/A' ? null : (int)$oData->totalSeasons;
        $oTitle->dvd              = isset($oData->DVD) && $oData->DVD !== 'N/A' ? Carbon::parse($oData->DVD)->format('Y-m-d') : null;
        $oTitle->boxOffice        = !isset($oData->BoxOffice) || $oData->BoxOffice === 'N/A' ? null : $oData->BoxOffice;
        $oTitle->production       = !isset($oData->Production) || $oData->Production === 'N/A' ? null : $oData->Production;
        $oTitle->website          = !isset($oData->Website) || $oData->Website === 'N/A' ? null : $oData->Website;

        $oTitle->genresDisplay    = implode(', ', $oTitle->genres);
        $oTitle->directorsDisplay = implode(', ', $oTitle->directors);
        $oTitle->writersDisplay   = implode(', ', $oTitle->writers);
        $oTitle->actorsDisplay    = implode(', ', $oTitle->actors);
        $oTitle->countries        = explode(', ', $oTitle->countriesDisplay);

        foreach ((array)$oData->Ratings as $rating) {
            $oTitle->ratings[] = new Rating($rating->Source, $rating->Value);
        }

        return $oTitle;
    }
}
