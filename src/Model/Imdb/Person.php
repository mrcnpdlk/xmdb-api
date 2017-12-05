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


class Person
{
    /**
     * @var string
     */
    public $id;
    /**
     * @var string
     */
    public $name;
    /**
     * @var \mrcnpdlk\Xmdb\Model\Imdb\Image
     */
    public $image;

    public function __construct(string $id, string $name, Image $oImage = null)
    {
        $this->id    = $id;
        $this->name  = $name;
        $this->image = $oImage;
    }
}
