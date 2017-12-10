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
 * Created by Marcin Pudełek <marcin@pudelek.org.pl>
 * Date: 29.11.2017
 * Time: 16:56
 */

namespace mrcnpdlk\Xmdb;


class Api
{
    /**
     * @var \mrcnpdlk\Xmdb\Api
     */
    protected static $instance;
    /**
     * @var \mrcnpdlk\Xmdb\Client
     */
    private $oClient;

    /**
     * @var \mrcnpdlk\Xmdb\Tmdb
     */
    private $oTmdbApi;
    /**
     * @var \mrcnpdlk\Xmdb\Imdb
     */
    private $oImdbApi;
    /**
     * @var \mrcnpdlk\Xmdb\Omdb
     */
    private $oOmdbApi;


    /**
     * Api constructor.
     *
     * @param \mrcnpdlk\Xmdb\Client $oClient
     */
    protected function __construct(Client $oClient)
    {
        $this->oClient = $oClient;
    }

    /**
     * @param \mrcnpdlk\Xmdb\Client $oClient
     *
     * @return \mrcnpdlk\Xmdb\Api
     */
    public static function create(Client $oClient): Api
    {
        if (null === static::$instance) {
            static::$instance = new static($oClient);
        }

        return static::$instance;
    }

    /**
     * @return \mrcnpdlk\Xmdb\Api
     * @throws \mrcnpdlk\Xmdb\Exception
     */
    public static function getInstance(): Api
    {
        if (null === static::$instance) {
            throw new Exception(sprintf('First call CREATE method!'));
        }

        return static::$instance;
    }

    /**
     * @return \mrcnpdlk\Xmdb\Client
     */
    public function getClient(): Client
    {
        return $this->oClient;
    }

    /**
     * @return \mrcnpdlk\Xmdb\Imdb
     * @throws \mrcnpdlk\Xmdb\Exception
     */
    public function getImdbApi(): Imdb
    {
        if (null === $this->oImdbApi) {
            $this->oImdbApi = new Imdb($this->oClient);
        }

        return $this->oImdbApi;
    }

    /**
     * @return \mrcnpdlk\Xmdb\Omdb
     * @throws \mrcnpdlk\Xmdb\Exception
     */
    public function getOmdbApi(): Omdb
    {
        if (null === $this->oOmdbApi) {
            $this->oOmdbApi = new Omdb($this->oClient);
        }

        return $this->oOmdbApi;
    }

    /**
     * @return \mrcnpdlk\Xmdb\Tmdb
     * @throws \mrcnpdlk\Xmdb\Exception
     */
    public function getTmdbApi(): Tmdb
    {
        if (null === $this->oTmdbApi) {
            $this->oTmdbApi = new Tmdb($this->oClient);
        }

        return $this->oTmdbApi;
    }
}
