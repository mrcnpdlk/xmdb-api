<?php
/**
 * xMDB-API
 *
 * Copyright © 2017 pudelek.org.pl
 *
 * @license MIT License (MIT)
 *
 * For the full copyright and license information, please view source file
 * that is bundled with this package in the file LICENSE
 *
 * @author  Marcin Pudełek <marcin@pudelek.org.pl>
 */

/**
 * Created by Marcin.
 * Date: 01.12.2017
 * Time: 22:52
 */

namespace mrcnpdlk\Xmdb;


use Campo\UserAgent;
use Curl\Curl;
use HttpLib\Http;
use Imdb\Cache;
use Imdb\Config;
use Imdb\TitleSearch;
use KHerGe\JSON\JSON;
use mrcnpdlk\Xmdb\Model\Imdb\Character;
use mrcnpdlk\Xmdb\Model\Imdb\Image;
use mrcnpdlk\Xmdb\Model\Imdb\Info;
use mrcnpdlk\Xmdb\Model\Imdb\Person;
use mrcnpdlk\Xmdb\Model\Imdb\Rating;
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
     * @var \Imdb\Config
     */
    private $oConfig;
    /** @noinspection PhpUndefinedClassInspection */

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $oLog;
    /**
     * @var \Psr\SimpleCache\CacheInterface
     */
    private $oCache;

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
            $this->oCache                 = $oClient->getAdapter()->getCache();
            $this->oConfig                = new Config();
            $this->oConfig->usecache      = null !== $this->oCache;
            $this->oConfig->language      = $oClient->getLang();
            $this->oConfig->default_agent = UserAgent::random();


        } catch (\Exception $e) {
            throw new Exception(sprintf('Cannot create Tmdb Client'), 1, $e);
        }
    }

    /**
     * @param string $imdbId
     *
     * @return \Imdb\Title
     */
    protected function getApiTitle(string $imdbId)
    {
        return new \Imdb\Title($imdbId, $this->oConfig, $this->oLog, $this->oCache);
    }

    /**
     * @return \Imdb\TitleSearch
     */
    protected function getApiTitleSearch()
    {
        return new \Imdb\TitleSearch($this->oConfig, $this->oLog, $this->oCache);
    }

    /**
     * @param string $imdbId
     *
     * @return \mrcnpdlk\Xmdb\Model\Imdb\Info
     * @throws \mrcnpdlk\Xmdb\Exception
     */
    public function getInfo(string $imdbId): Info
    {
        try {
            $searchUrl = 'http://app.imdb.com/title/maindetails?tconst=' . $imdbId;

            $oResp = $this->oClient->getAdapter()->useCache(
                function () use ($searchUrl) {
                    $oCurl = new Curl();
                    $oCurl->setUserAgent(UserAgent::random());
                    $oCurl->setHeader('Accept-Language', $this->oClient->getLang());
                    $oCurl->get($searchUrl);

                    if ($oCurl->error) {
                        throw new \RuntimeException('Curl Error! ' . Http::message($oCurl->httpStatusCode), $oCurl->error_code);
                    }

                    return $oCurl->response->data ?? null;
                },
                [$searchUrl, $this->oClient->getLang()],
                3600 * 2)
            ;
            $oData = $oResp->data ?? $oResp;

            $oInfo              = new Info();
            $oInfo->id          = $oData->tconst;
            $oInfo->title       = $oData->title;
            $oInfo->year        = $oData->year;
            $oInfo->image       = isset($oData->image) ? new Image($oData->image->url, $oData->image->width, $oData->image->height) : null;
            $oInfo->releaseDate = $oData->release_date->normal ?? null;
            $oInfo->genres      = $oData->genres;
            $oInfo->rating      = $oData->rating;
            $oInfo->votes       = $oData->num_votes;
            $oInfo->runtime     = $oData->runtime->time ?? null;

            $tmp = [];
            foreach ($oData->directors_summary ?? [] as $oDir) {
                $oPerson            = $oDir->name;
                $oImage             = isset($oPerson->image) ? new Image($oPerson->image->url, $oPerson->image->width,
                    $oPerson->image->height) : null;
                $oInfo->directors[] = new Person($oPerson->nconst, $oPerson->name, $oImage);
                $tmp[] = $oPerson->name;
            }
            $oInfo->directorsDisplay = implode(', ', $tmp);

            $tmp = [];
            foreach ($oData->writers_summary ?? [] as $oDir) {
                $oPerson          = $oDir->name;
                $oImage           = isset($oPerson->image) ? new Image($oPerson->image->url, $oPerson->image->width,
                    $oPerson->image->height) : null;
                $oInfo->writers[] = new Person($oPerson->nconst, $oPerson->name, $oImage);
                $tmp[] = $oPerson->name;
            }
            $oInfo->writersDisplay = implode(', ', $tmp);

            foreach ($oData->photos ?? [] as $photo) {
                $oInfo->photos[] = new Image($photo->image->url, $photo->image->width, $photo->image->height);
            }

            foreach ($oData->cast_summary ?? [] as $ch) {
                $oImage        = $ch->name->image ? new Image($ch->name->image->url, $ch->name->image->width,
                    $ch->name->image->height) : null;
                $oPerson       = $ch->name ? new Person($ch->name->nconst, $ch->name->name, $oImage) : null;
                $oInfo->cast[] = new Character($ch->char, $oPerson);
            }

            $oApiTitle        = $this->getApiTitle($imdbId);
            $oInfo->countries = $oApiTitle->country();


            $oInfo->genresDisplay    = implode(', ', $oInfo->genres);
            $oInfo->countriesDisplay = implode(', ', $oInfo->countries);

            return $oInfo;

        } catch (\Exception $e) {
            throw new Exception(sprintf('Item [%s] not found, reason: %s', $imdbId, $e->getMessage()));
        }
    }

    /**
     * @param string $imdbId
     *
     * @return \mrcnpdlk\Xmdb\Model\Imdb\Rating
     * @throws \mrcnpdlk\Xmdb\Exception
     */
    public function getRating(string $imdbId): Rating
    {
        try {
            $searchUrl = 'http://p.media-imdb.com/static-content/documents/v1/title/'
                . $imdbId
                . '/ratings%3Fjsonp=imdb.rating.run:imdb.api.title.ratings/data.json?u='
                . $this->oClient->getImdbUser();

            $oResp = $this->oClient->getAdapter()->useCache(
                function () use ($searchUrl) {
                    $oCurl = new Curl();
                    $oCurl->setOpt(\CURLOPT_ENCODING, 'gzip');
                    $oCurl->setUserAgent(UserAgent::random());
                    $oCurl->setHeader('Accept-Language', $this->oClient->getLang());
                    $oCurl->get($searchUrl);

                    if ($oCurl->error) {
                        throw new \RuntimeException('Curl Error! ' . Http::message($oCurl->httpStatusCode), $oCurl->error_code);
                    }

                    preg_match("/^[\w\.]*\((.*)\)$/", $oCurl->response, $output_array);
                    $json = new JSON();

                    return $json->decode($output_array[1]);
                },
                [$searchUrl, $this->oClient->getLang()],
                180)
            ;

            if (!isset($oResp->resource)) {
                throw new \RuntimeException('Resource is empty');
            }

            $oData = $oResp->resource;

            $oRating         = new Rating();
            $oRating->id     = $imdbId;
            $oRating->title  = $oData->title;
            $oRating->year   = $oData->year;
            $oRating->rating = $oData->rating;
            $oRating->votes  = $oData->ratingCount;
            $oRating->type   = $oData->titleType;

            return $oRating;

        } catch (\Exception $e) {
            throw new Exception(sprintf('Item [%s] not found, reason: %s', $imdbId, $e->getMessage()));
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
         * @var Title[] $tmpList
         */
        $answer      = [];
        $tmpList     = [];
        $tNativeList = $this->searchByTitleNative($title);
        foreach ($tNativeList as $item) {
            $tmpList[$item->imdbId] = $item;
        }

        $tApiList = $this->searchByTitleApi($title);
        foreach ($tApiList as $item) {
            if (!\array_key_exists($item->imdbId, $tmpList)) {
                $tmpList[$item->imdbId] = $item;
            } else {
                $tmpList[$item->imdbId]->isMovie = $tmpList[$item->imdbId]->isMovie ?? $item->isMovie;
            }
        }

        foreach ($tmpList as $item) {
            $answer[] = $item;
        }

        return $answer;
    }

    /**
     * @param string $title
     *
     * @return Title[]
     */
    public function searchByTitleApi(string $title): array
    {
        try {
            $answer = [];
            $tList  = $this->getApiTitleSearch()->search($title, [
                TitleSearch::MOVIE,
                TitleSearch::TV_SERIES,
                TitleSearch::VIDEO,
                TitleSearch::TV_MOVIE,
            ])
            ;

            foreach ($tList as $element) {
                $oTitle                  = new Title();
                $oTitle->imdbId          = 'tt' . $element->imdbid();
                $oTitle->title           = $element->title();
                $oTitle->rating          = null; //set null for speedy
                $oTitle->episode         = null;
                $oTitle->year            = empty($element->year()) ? null : $element->year();
                $oTitle->type            = $element->movietype();
                $oTitle->isMovie         = \in_array($element->movietype(), [TitleSearch::MOVIE, TitleSearch::TV_MOVIE, TitleSearch::VIDEO],
                    true);
                $oTitle->director        = [];
                $oTitle->directorDisplay = implode(', ', $oTitle->director);
                $oTitle->star            = [];
                $oTitle->starDisplay     = implode(', ', $oTitle->star);
                $answer[]                = $oTitle;
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

                $metascoreNode  = $content->find('div.ratings-bar div.ratings-metascore', 0);
                $foundMetascore = null;
                if ($metascoreNode) {
                    $oSpanNode      = $metascoreNode->find('span', 0);
                    $foundMetascore = $oSpanNode ? (int)$oSpanNode->text() : null;
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
                    $oTitle->metascore       = $foundMetascore;
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
}
