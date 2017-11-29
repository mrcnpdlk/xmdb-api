<?php
/**
 * Created by Marcin Pudełek <marcin@pudelek.org.pl>
 * Date: 29.11.2017
 * Time: 16:56
 */

namespace mrcnpdlk\Xmdb;


use Tmdb\ApiToken;

class Api
{
    /**
     * @var \mrcnpdlk\Xmdb\Api
     */
    protected static $instance = null;
    /**
     * @var \mrcnpdlk\Xmdb\Client
     */
    private $oClient;
    /**
     * @var \Tmdb\Client
     */
    private $oTmdbClient;
    /**
     * @var mixed
     */
    private $oImdbClient;

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
        if (!isset(static::$instance)) {
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
        if (!isset(static::$instance)) {
            throw new Exception(sprintf('First call CREATE method!'));
        }

        return static::$instance;
    }

    /**
     * @return \Tmdb\Client
     * @throws \mrcnpdlk\Xmdb\Exception
     */
    public function getTmdbClient()
    {
        try {
            if (null === $this->oTmdbClient) {
                $oCache  = $this->getClient()->getAdapter()->getCache();
                $options = [
                    'cache' => [
                        'enabled' => null !== $oCache,
                        'handler' => $oCache ?: null,
                    ],
                    'log'   => [
                        'enabled' => true,
                        'handler' => $this->getClient()->getLogger(),
                        'level'   => 'debug',
                    ],
                ];

                $oToken            = new ApiToken($this->getClient()->getTmdbToken());
                $this->oTmdbClient = new \Tmdb\Client($oToken, $options);
            }

            return $this->oTmdbClient;
        } catch (\Exception $e) {
            throw new Exception(sprintf('Cannot create Tmdb Client'), 1, $e);
        }

    }

    /**
     * @return \mrcnpdlk\Xmdb\Client
     */
    public function getClient()
    {
        return $this->oClient;
    }
}