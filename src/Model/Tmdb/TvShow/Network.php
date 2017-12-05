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
 * @author Marcin Pudełek <marcin@pudelek.org.pl>
 */

namespace mrcnpdlk\Xmdb\Model\Tmdb\TvShow;


class Network
{
    /**
     * @var integer
     */
    public $id;
    /**
     * @var string
     */
    public $name;

    /**
     * Network constructor.
     *
     * @param int    $id
     * @param string $name
     */
    public function __construct(int $id, string $name)
    {
        $this->id   = $id;
        $this->name = $name;
    }
}
