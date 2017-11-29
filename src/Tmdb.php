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
use mrcnpdlk\Xmdb\Model\Tmdb\Title;
use RuntimeException;
use Tmdb\ApiToken;

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
                    'language'        => 'pl',
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
            } elseif (\count($find['movie_results']) === 1 && \count($find['tv_results']) === 1 && !empty($find['tv_results']) && !empty($find['movie_results'])) {
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
     */
    public function getMovie(int $id): Model\Tmdb\Movie\Title
    {
        $tData                = $this->oTmdbClient->getMoviesApi()->getMovie($id, [
            'language'      => 'pl',
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
    }

    public function getTvShow(int $id)
    {
        $tData = $this->oTmdbClient
            ->getTvApi()
            ->getTvshow($id, [
                'language'      => 'pl',
                'include_adult' => true,
            ])
        ;

        return $tData;
    }
}
