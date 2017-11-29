<?php
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

}
