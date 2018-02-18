<?php
/**
 * Created by Marcin.
 * Date: 18.02.2018
 * Time: 20:00
 */

namespace mrcnpdlk\Xmdb;


use Imdb\CacheInterface;

class ImdbCache implements CacheInterface
{
    /**
     * @var \Psr\SimpleCache\CacheInterface
     */
    private $oPsrCache;
    /**
     * @var integer
     */
    private $ttl;

    public function __construct(\Psr\SimpleCache\CacheInterface $oCache, integer $ttl = null)
    {
        $this->oPsrCache = $oCache;
        $this->ttl       = $ttl ?? 3600;
    }

    public function get($key)
    {
        return $this->oPsrCache->get($key);
    }

    public function purge()
    {
        return $this;
    }

    public function set($key, $value)
    {
        $this->oPsrCache->set($key, $value, $this->ttl);

        return $this;
    }
}
