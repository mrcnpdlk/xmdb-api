<?php
/**
 * Created by Marcin.
 * Date: 01.12.2017
 * Time: 22:52
 */

namespace mrcnpdlk\Xmdb;


use Campo\UserAgent;
use Curl\Curl;
use Imdb\Cache;
use Imdb\Config;
use Imdb\TitleSearch;
use mrcnpdlk\Xmdb\Model\Imdb\Title;
use Sunra\PhpSimple\HtmlDomParser;

class Imdb
{

    const ANONYMOUS_URL = 'http://anonymouse.org/cgi-bin/anon-www.cgi/';

    /**
     * @var \mrcnpdlk\Xmdb\Client
     */
    private $oClient;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $oLog;

    /**
     * Imdb constructor.
     *
     * @param \mrcnpdlk\Xmdb\Client $oClient
     *
     * @throws \mrcnpdlk\Xmdb\Exception
     */
    public function __construct(Client $oClient)
    {
        try {
            $this->oClient                = $oClient;
            $this->oLog                   = $oClient->getLogger();
            $this->oConfig                = new Config();
            $this->oConfig->language      = $oClient->getLang();
            $this->oConfig->default_agent = UserAgent::random();


        } catch (\Exception $e) {
            throw new Exception(sprintf('Cannot create Tmdb Client'), 1, $e);
        }
    }

    /**
     * Combined search by title
     *
     * @param string $title
     *
     * @return Title[]
     */
    public function searchByTitle(string $title): array
    {
        /**
         * @var Title[] $answer
         */
        $answer      = [];
        $tAddedIds   = [];
        $tNativeList = $this->searchByTitleNative($title);
        foreach ($tNativeList as $item) {
            $answer[]    = $item;
            $tAddedIds[] = $item->imdbId;
        }

        $tApiList = $this->searchByTitleApi($title);
        foreach ($tApiList as $item) {
            if (!\in_array($item->imdbId, $tAddedIds)) {
                $answer[]    = $item;
                $tAddedIds[] = $item->imdbId;
            }
        }

        return $answer;
    }

    /**
     * @param string $title
     *
     * @return Title[]
     */
    public function searchByTitleNative(string $title): array
    {
        try {
            /**
             * @var Title[] $answer
             */
            $answer = [];
            $searchUrl
                    = 'http://www.imdb.com/search/title' .
                '?at=0&sort=num_votes&title_type=feature,tv_movie,tv_series,tv_episode,tv_special,mini_series,documentary,short,video&title='
                . $title;

            $htmlContent = $this->oClient->getAdapter()->useCache(
                function () use ($searchUrl) {
                    $oCurl = new Curl();
                    $oCurl->setHeader('Accept-Language', $this->oClient->getLang());

                    return $oCurl->get($searchUrl);
                },
                [$searchUrl, $this->oClient->getLang()],
                3600 * 2)
            ;

            $html = HtmlDomParser::str_get_html($htmlContent);
            if (!$html) {
                throw new \RuntimeException('Response from IMDB malformed!');
            }
            $listerItemNode = $html->find('div.lister-item');

            foreach ($listerItemNode as $element) {
                $content = $element->find('div.lister-item-content', 0);
                if (!$content) {
                    throw new \RuntimeException('Empty search result!');
                }
                $titleNode = $content->find('h3.lister-item-header a', 0);
                if ($titleNode) {
                    $foundTitle = trim($titleNode->text());
                } else {
                    throw new \RuntimeException('Title not found!');
                }
                $yearNode = $content->find('h3.lister-item-header .lister-item-year', 0);
                if ($yearNode) {
                    $foundYear = trim($yearNode->text());
                    preg_match("/^\(?([0-9\-\–\s]+)([\w\s]*)?\)?$/u", $foundYear, $yearOut);
                    $foundYear = isset($yearOut[1]) ? trim($yearOut[1]) : $foundYear;
                    $foundType = isset($yearOut[2]) && trim($yearOut[2]) ? trim($yearOut[2]) : null;
                } else {
                    $foundYear = null;
                    $foundType = null;
                }
                $episodeNode  = $content->find('h3.lister-item-header a', 1);
                $foundEpisode = null;
                if ($episodeNode) {
                    $foundEpisode = trim($episodeNode->text());
                }
                $ratingNode  = $content->find('div.ratings-bar div.ratings-imdb-rating', 0);
                $foundRating = null;
                if ($ratingNode) {
                    $foundRating = trim($ratingNode->text());
                }

                $idNode = $element->find('div.lister-top-right div.ribbonize', 0);
                $imdbId = null;
                if ($idNode) {
                    $imdbId = $idNode->getAttribute('data-tconst');
                }

                $directors   = [];
                $stars       = [];
                $personsNode = $content->find('p', 2);
                if ($personsNode) {
                    $persons = $personsNode->find('a');
                    if (\is_array($persons)) {
                        foreach ($persons as $person) {
                            if (strpos($person->getAttribute('href'), 'adv_li_dr_') !== false) {
                                $directors[] = $person->text();
                            }
                            if (strpos($person->getAttribute('href'), 'adv_li_st_') !== false) {
                                $stars[] = $person->text();
                            }
                        }
                    }
                }

                if ($imdbId) {
                    $oTitle                  = new Title();
                    $oTitle->title           = $foundTitle;
                    $oTitle->imdbId          = $imdbId;
                    $oTitle->rating          = $foundRating;
                    $oTitle->episode         = $foundEpisode;
                    $oTitle->year            = $foundYear;
                    $oTitle->type            = $foundType;
                    $oTitle->director        = $directors;
                    $oTitle->directorDisplay = implode(', ', $oTitle->director);
                    $oTitle->star            = $stars;
                    $oTitle->starDisplay     = implode(', ', $oTitle->star);

                    $answer[] = $oTitle;
                }

            }

            return $answer;
        } catch (\Exception $e) {
            $this->oLog->warning(sprintf('Item [%s] not found, reason: %s', $title, $e->getMessage()));

            return [];
        }
    }

    /**
     * @param string $title
     *
     * @param bool   $extendedSearch
     *
     * @return Title[]
     */
    public function searchByTitleApi(string $title, bool $extendedSearch = true): array
    {
        $answer       = [];
        $oTitleSearch = new TitleSearch($this->oConfig, $this->oLog, new Cache($this->oConfig, $this->oLog));
        $tList        = $oTitleSearch->search($title, [
            TitleSearch::MOVIE,
            TitleSearch::TV_SERIES,
            TitleSearch::VIDEO,
            TitleSearch::TV_MOVIE,
        ]);

        foreach ($tList as $element) {
            $oTitle                  = new Title();
            $oTitle->imdbId          = 'tt' . $element->imdbid();
            $oTitle->title           = $element->title();
            $oTitle->rating          = null; //set null for speedy
            $oTitle->episode         = null;
            $oTitle->year            = empty($element->year()) ? null : $element->year();
            $oTitle->type            = $element->movietype();
            $oTitle->isMovie         = \in_array($element->movietype(), [TitleSearch::MOVIE, TitleSearch::TV_MOVIE, TitleSearch::VIDEO], true);
            $oTitle->director        = [];
            $oTitle->directorDisplay = implode(', ', $oTitle->director);
            $oTitle->star            = [];
            $oTitle->starDisplay     = implode(', ', $oTitle->star);
            $answer[]                = $oTitle;
        }

        return $answer;
    }
}
