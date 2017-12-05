<?php
/**
 * Created by Marcin.
 * Date: 29.11.2017
 * Time: 00:05
 */

namespace mrcnpdlk\Xmdb;

use mrcnpdlk\Psr16Cache\Adapter;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Psr\SimpleCache\CacheInterface;

/**
 * Class Client
 *
 * @package mrcnpdlk\Xmdb
 */
class Client
{

    /**
     * Cache handler
     *
     * @var CacheInterface
     */
    private $oCache;
    /**
     * @var Adapter
     */
    private $oCacheAdapter;
    /**
     * Logger handler
     *
     * @var \Psr\Log\LoggerInterface
     */
    private $oLogger;
    /**
     * @var string
     */
    private $sTmdbToken;
    /**
     * @var string
     */
    private $sImdbUser;
    /**
     * Language code
     *
     * @var string
     */
    private $sLangCode;
    /**
     * @var string
     */
    private $sOmdbToken;

    /**
     * Client constructor.
     *
     */
    public function __construct()
    {
        $this->setLoggerInstance();
        $this->setCacheInstance();
    }

    /**
     * Set Logger handler (PSR-3)
     *
     * @param \Psr\Log\LoggerInterface|null $oLogger
     *
     * @return $this
     */
    public function setLoggerInstance(LoggerInterface $oLogger = null)
    {
        $this->oLogger = $oLogger ?: new NullLogger();
        $this->setCacheAdapter();

        return $this;
    }

    /**
     * Setting Cache Adapter
     *
     * @return $this
     */
    private function setCacheAdapter()
    {
        $this->oCacheAdapter = new Adapter($this->oCache, $this->oLogger);

        return $this;
    }

    /**
     * Set Cache handler (PSR-16)
     *
     * @param \Psr\SimpleCache\CacheInterface|null $oCache
     *
     * @return \mrcnpdlk\Xmdb\Client
     * @see https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-16-simple-cache.md PSR-16
     */
    public function setCacheInstance(CacheInterface $oCache = null): Client
    {
        $this->oCache = $oCache;
        $this->setCacheAdapter();

        return $this;
    }

    /**
     * Get logger instance
     *
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->oLogger;
    }

    /**
     * @param string $token
     *
     * @return \mrcnpdlk\Xmdb\Client
     */
    public function setTmdbToken(string $token): Client
    {
        $this->sTmdbToken = $token;

        return $this;
    }

    /**
     * @return string
     * @throws \mrcnpdlk\Xmdb\Exception
     */
    public function getTmdbToken(): string
    {
        if (empty($this->sTmdbToken)) {
            throw new Exception('Tmdb Token not set');
        }

        return $this->sTmdbToken;
    }

    /**
     * @return string
     * @throws \mrcnpdlk\Xmdb\Exception
     */
    public function getOmdbToken(): string
    {
        if (empty($this->sOmdbToken)) {
            throw new Exception('Tmdb Token not set');
        }

        return $this->sOmdbToken;
    }

    /**
     * @param string $user
     *
     * @return \mrcnpdlk\Xmdb\Client
     */
    public function setOmdbToken(string $user): Client
    {
        $this->sOmdbToken = $user;

        return $this;
    }

    /**
     * @param string $user
     *
     * @return \mrcnpdlk\Xmdb\Client
     */
    public function setImdbUser(string $user): Client
    {
        $this->sImdbUser = $user;

        return $this;
    }

    /**
     * @return string
     * @throws \mrcnpdlk\Xmdb\Exception
     */
    public function getImdbUser(): string
    {
        if (empty($this->sImdbUser)) {
            throw new Exception('Imdb User not set');
        }

        return $this->sImdbUser;
    }

    /**
     * @return \mrcnpdlk\Psr16Cache\Adapter
     */
    public function getAdapter(): Adapter
    {
        return $this->oCacheAdapter;
    }

    /**
     * @param string $lang ISO 639-1 language code
     *
     * @return $this
     */
    public function setLang(string $lang = 'en')
    {
        $this->sLangCode = strtolower($lang);

        return $this;
    }

    /**
     * @return string
     */
    public function getLang()
    {
        return $this->sLangCode;
    }
}
