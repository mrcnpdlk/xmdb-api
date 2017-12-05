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

/**
 * Created by Marcin.
 * Date: 30.11.2017
 * Time: 00:13
 */

namespace mrcnpdlk\Xmdb\Model\Tmdb;


class Country
{
    /**
     * ISO 3166 code
     *
     * @var string
     */
    public $id;
    /**
     * @var string
     */
    public $name;

    public function __construct(string $id, string $name)
    {
        $this->id   = $id;
        $this->name = $name;
    }
}
