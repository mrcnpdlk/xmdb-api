<?php
/**
 * Created by Marcin.
 * Date: 29.11.2017
 * Time: 21:54
 */

namespace mrcnpdlk\Xmdb;


use Carbon\Carbon;
use mrcnpdlk\Xmdb\Exception\NotFound;
use mrcnpdlk\Xmdb\Model\Tmdb\Company;
use mrcnpdlk\Xmdb\Model\Tmdb\Country;
use mrcnpdlk\Xmdb\Model\Tmdb\Genre;
use mrcnpdlk\Xmdb\Model\Tmdb\Language;
use mrcnpdlk\Xmdb\Model\Tmdb\Movie\Collection;
use mrcnpdlk\Xmdb\Model\Tmdb\Movie\Title as MovieTitle;
use mrcnpdlk\Xmdb\Model\Tmdb\Person;
use mrcnpdlk\Xmdb\Model\Tmdb\Title;
use mrcnpdlk\Xmdb\Model\Tmdb\TvShow\Network;
use mrcnpdlk\Xmdb\Model\Tmdb\TvShow\Season;
use mrcnpdlk\Xmdb\Model\Tmdb\TvShow\Title as TvShowTitle;
use RuntimeException;
use Tmdb\ApiToken;
use Tmdb\Exception\TmdbApiException;

class Tmdb
{
    /**
     * @var \mrcnpdlk\Xmdb\Client
     */
    private $oClient;
    /**
     * @var \Tmdb\Client
     */
    private $oTmdbClient;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $oLog;

    /**
     * Tmdb constructor.
     *
     * @param \mrcnpdlk\Xmdb\Client $oClient
     *
     * @throws \mrcnpdlk\Xmdb\Exception
     */
    public function __construct(Client $oClient)
    {
        $this->oClient = $oClient;
        $this->oLog    = $oClient->getLogger();
        try {
            if (null === $this->oTmdbClient) {
                $options = [
                    'cache' => [
                        'enabled' => true,
                    ],
                    'log'   => [
                        'enabled' => true,
                    ],
                ];

                $oToken            = new ApiToken($this->oClient->getTmdbToken());
                $this->oTmdbClient = new \Tmdb\Client($oToken, $options);
            }
        } catch (\Exception $e) {
            throw new Exception(sprintf('Cannot create Tmdb Client'), 1, $e);
        }
    }

    /**
     * @param string|null $imdbId
     *
     * @return Title
     * @throws \mrcnpdlk\Xmdb\Exception\NotFound Title not found
     * @throws \mrcnpdlk\Xmdb\Exception
     */
    public function getByImdbId(string $imdbId = null)
    {
        try {
            $this->oLog->info(sprintf('Searching: %s', $imdbId));
            if ($imdbId === null || $imdbId === '') {
                throw new RuntimeException('ImdbId is require!');
            }

            $oTitle = new Title();
            $find   = $this->oTmdbClient
                ->getFindApi()
                ->findBy($imdbId, [
                    'external_source' => 'imdb_id',
                    'language'        => $this->oClient->getLang(),
                    'include_adult'   => true,
                ])
            ;
            if (!empty($find['movie_results']) && \count($find['movie_results']) === 1) {
                $item = $find['movie_results'][0];

                $oTitle->isAdult      = isset($item['adult']) ? (bool)$item['adult'] : false;
                $oTitle->title        = $item['title'];
                $oTitle->titleOrg     = $item['original_title'];
                $oTitle->titleOrgLang = $item['original_language'];
                $oTitle->id           = $item['id'];
                $oTitle->imdbId       = $imdbId;
                $oTitle->backdrop     = $item['backdrop_path'];
                $oTitle->poster       = $item['poster_path'];
                $oTitle->releaseDate  = $item['release_date'];
                $oTitle->rating       = $item['vote_average'];
                $oTitle->voteCount    = $item['vote_count'];
                $oTitle->popularity   = $item['popularity'];
                $oTitle->isMovie      = true;
                $oTitle->overview     = $item['overview'];
                $oTitle->releaseYear  = $oTitle->releaseDate ? Carbon::parse($oTitle->releaseDate)->format('Y') : null;
            } elseif (!empty($find['tv_results']) && \count($find['tv_results']) === 1) {
                $item = $find['tv_results'][0];

                $oTitle->isAdult      = isset($item['adult']) ? (bool)$item['adult'] : false;
                $oTitle->title        = $item['name'];
                $oTitle->titleOrg     = $item['original_name'];
                $oTitle->titleOrgLang = $item['original_language'];
                $oTitle->id           = $item['id'];
                $oTitle->imdbId       = $imdbId;
                $oTitle->backdrop     = $item['backdrop_path'];
                $oTitle->poster       = $item['poster_path'];
                $oTitle->releaseDate  = $item['first_air_date'];
                $oTitle->rating       = $item['vote_average'];
                $oTitle->voteCount    = $item['vote_count'];
                $oTitle->popularity   = $item['popularity'];
                $oTitle->isMovie      = false;
                $oTitle->overview     = $item['overview'];
                $oTitle->releaseYear  = $oTitle->releaseDate ? Carbon::parse($oTitle->releaseDate)->format('Y') : null;
            } elseif (\count($find['movie_results']) === 1 && \count($find['tv_results']) === 1 && !empty($find['tv_results'])
                && !empty($find['movie_results'])) {
                throw new RuntimeException('Too many items in TMDB database');
            } else {
                throw new NotFound('TMDB response empty');
            }

            return $oTitle;
        } catch (NotFound $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param int $id
     *
     * @return \mrcnpdlk\Xmdb\Model\Tmdb\Movie\Title
     * @throws \mrcnpdlk\Xmdb\Exception\NotFound
     * @throws \mrcnpdlk\Xmdb\Exception
     */
    public function getMovie(int $id): Model\Tmdb\Movie\Title
    {
        try {
            $tData                = $this->oTmdbClient->getMoviesApi()->getMovie($id, [
                'language'      => $this->oClient->getLang(),
                'include_adult' => true,
            ])
            ;
            $oTitle               = new MovieTitle();
            $oTitle->id           = $id;
            $oTitle->title        = $tData['title'];
            $oTitle->titleOrg     = $tData['original_title'];
            $oTitle->titleOrgLang = $tData['original_language'];
            $oTitle->isVideo      = $tData['video'];
            $oTitle->isAdult      = $tData['adult'];
            $oTitle->imdbId       = $tData['imdb_id'];
            $oTitle->backdrop     = $tData['backdrop_path'];
            $oTitle->poster       = $tData['poster_path'];
            $oTitle->releaseDate  = $tData['release_date'];
            $oTitle->releaseYear  = null;
            $oTitle->rating       = $tData['vote_average'];
            $oTitle->voteCount    = $tData['vote_count'];
            $oTitle->popularity   = $tData['popularity'];
            $oTitle->overview     = $tData['overview'];
            $oTitle->homepage     = $tData['homepage'];
            $oTitle->budget       = $tData['budget'];
            $oTitle->revenue      = $tData['revenue'];
            $oTitle->runtime      = $tData['runtime'];
            $oTitle->status       = $tData['status'];
            $oTitle->tagline      = $tData['tagline'];

            foreach ($tData['genres'] ?? [] as $g) {
                $oTitle->genres[] = new Genre($g['id'], $g['name']);
            }
            foreach ($tData['production_companies'] ?? [] as $c) {
                $oTitle->productionCompanies[] = new Company($c['id'], $c['name']);
            }
            foreach ($tData['production_countries'] ?? [] as $g) {
                $oTitle->productionCountries[] = new Country($g['iso_3166_1'], $g['name']);
            }
            foreach ($tData['spoken_languages'] ?? [] as $g) {
                $oTitle->spokenLanguages[] = new Language($g['iso_639_1'], $g['name']);
            }
            if (!empty($tData['belongs_to_collection'])) {
                $oTitle->collection = new Collection(
                    $tData['belongs_to_collection']['id'],
                    $tData['belongs_to_collection']['name'],
                    $tData['belongs_to_collection']['poster_path'],
                    $tData['belongs_to_collection']['backdrop_path']);
            }

            return $oTitle;
        } catch (\Exception $e) {
            if ($e instanceof TmdbApiException) {
                if ($e->getCode() === 34) {
                    throw new NotFound($e->getMessage());
                }
                throw new Exception(sprintf('TMDB Response Error: %s', $e->getMessage()));
            }

            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param int $id
     *
     * @return \mrcnpdlk\Xmdb\Model\Tmdb\TvShow\Title
     * @throws \mrcnpdlk\Xmdb\Exception\NotFound
     * @throws \mrcnpdlk\Xmdb\Exception
     */
    public function getTvShow(int $id): TvShowTitle
    {
        try {
            $tData = $this->oTmdbClient
                ->getTvApi()
                ->getTvshow($id, [
                    'language'           => $this->oClient->getLang(),
                    'include_adult'      => true,
                    'append_to_response' => 'external_ids',
                ])
            ;

            $oTitle                  = new TvShowTitle();
            $oTitle->id              = $id;
            $oTitle->title           = $tData['name'];
            $oTitle->titleOrg        = $tData['original_name'];
            $oTitle->titleOrgLang    = $tData['original_language'];
            $oTitle->inProduction    = $tData['in_production'];
            $oTitle->imdbId          = $tData['external_ids']['imdb_id'] ?: null;
            $oTitle->backdrop        = $tData['backdrop_path'];
            $oTitle->poster          = $tData['poster_path'];
            $oTitle->firstAirDate    = $tData['first_air_date'];
            $oTitle->lastAirDate     = $tData['last_air_date'];
            $oTitle->rating          = $tData['vote_average'];
            $oTitle->voteCount       = $tData['vote_count'];
            $oTitle->popularity      = $tData['popularity'];
            $oTitle->overview        = $tData['overview'];
            $oTitle->homepage        = $tData['homepage'];
            $oTitle->episodeRuntimes = $tData['episode_run_time'];
            $oTitle->status          = $tData['status'];
            $oTitle->type            = $tData['type'];
            $oTitle->languages       = $tData['languages'];
            $oTitle->episodesNumber  = $tData['number_of_episodes'];
            $oTitle->seasonsNumber   = $tData['number_of_seasons'];
            $oTitle->originCountries = $tData['origin_country'];

            foreach ($tData['genres'] ?? [] as $item) {
                $oTitle->genres[] = new Genre($item['id'], $item['name']);
            }
            foreach ($tData['networks'] ?? [] as $item) {
                $oTitle->networks[] = new Network($item['id'], $item['name']);
            }
            foreach ($tData['production_companies'] ?? [] as $item) {
                $oTitle->productionCompanies[] = new Company($item['id'], $item['name']);
            }
            foreach ($tData['seasons'] ?? [] as $item) {
                $oTitle->seasons[] = new Season(
                    $item['id'],
                    $item['season_number'],
                    $item['episode_count'],
                    $item['air_date'],
                    $item['poster_path']
                );
            }
            foreach ($tData['created_by'] ?? [] as $item) {
                $oTitle->createdBy[] = new Person(
                    $item['id'],
                    $item['name'],
                    $item['gender'],
                    $item['profile_path']
                );
            }

            return $oTitle;
        } catch (\Exception $e) {
            if ($e instanceof TmdbApiException) {
                if ($e->getCode() === 34) {
                    throw new NotFound($e->getMessage());
                }
                throw new Exception(sprintf('TMDB Response Error: %s', $e->getMessage()));
            }

            throw new Exception($e->getMessage());
        }
    }
}
