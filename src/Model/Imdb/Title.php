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
 * Date: 02.12.2017
 * Time: 00:10
 */

namespace mrcnpdlk\Xmdb\Model\Imdb;

/**
 * Class Title
 *
 * @package mrcnpdlk\Xmdb\Model\Imdb
 */
class Title
{
    /**
     * @var string
     */
    public $title;
    /**
     * @var string
     */
    public $titleOrg;
    /**
     * @var boolean
     */
    public $isMovie;
    /**
     * @var string
     */
    public $imdbId;
    /**
     * @var float
     */
    public $rating;
    /**
     * @var integer|null
     */
    public $metascore;
    /**
     * @var string
     */
    public $episode;
    /**
     * @var string
     */
    public $year;
    /**
     * @var string
     */
    public $type;
    /**
     * @var string[]
     */
    public $director = [];
    /**
     * @var string
     */
    public $directorDisplay;
    /**
     * @var string[]
     */
    public $star = [];
    /**
     * @var string
     */
    public $starDisplay;
}
