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

namespace mrcnpdlk\Xmdb\Model\Imdb;


class Character
{
    /**
     * @var string
     */
    public $name;
    /**
     * @var \mrcnpdlk\Xmdb\Model\Imdb\Person
     */
    public $person;

    public function __construct(string $name, Person $oPerson = null)
    {
        $this->name   = $name;
        $this->person = $oPerson;
    }
}
