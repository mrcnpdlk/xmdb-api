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

/**
 * Class Info
 *
 * @package mrcnpdlk\Xmdb\Model\Imdb
 */
class Info
{
    /**
     * @var string
     */
    public $id;
    /**
     * @var string
     */
    public $title;
    /**
     * @var string
     */
    public $year;
    /**
     * @var string
     */
    public $releaseDate;
    /**
     * @var integer
     */
    public $runtime;
    /**
     * @var float
     */
    public $rating;
    /**
     * @var integer
     */
    public $votes;
    /**
     * @var \mrcnpdlk\Xmdb\Model\Imdb\Image
     */
    public $image;
    /**
     * @var \mrcnpdlk\Xmdb\Model\Imdb\Image[]
     */
    public $photos = [];
    /**
     * @var \mrcnpdlk\Xmdb\Model\Imdb\Person[]
     */
    public $directors = [];
    /**
     * @var \mrcnpdlk\Xmdb\Model\Imdb\Person[]
     */
    public $writers = [];
    /**
     * @var string[]
     */
    public $genres = [];
    /**
     * @var \mrcnpdlk\Xmdb\Model\Imdb\Character[]
     */
    public $cast = [];

}
