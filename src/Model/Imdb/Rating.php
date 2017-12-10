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
 * Class Rating
 *
 * @package mrcnpdlk\Xmdb\Model\Imdb
 */
class Rating
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
     * @var float
     */
    public $rating;
    /**
     * @var integer
     */
    public $votes;
    /**
     * @var string
     */
    public $type;
}
