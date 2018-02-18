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

/**
 * Created by Marcin.
 * Date: 29.11.2017
 * Time: 22:02
 */

namespace mrcnpdlk\Xmdb\Model\Tmdb;


class Title extends \mrcnpdlk\Xmdb\Model\Title
{
    /**
     * @var integer
     */
    public $id;
    /**
     * @var boolean
     */
    public $isMovie;
    /**
     * @var string
     */
    public $titleOrgLang;
    /**
     * @var boolean
     */
    public $isAdult;
    /**
     * @var string|null
     */
    public $backdrop;
    /**
     * @var string|null
     */
    public $poster;
    /**
     * @var float
     */
    public $rating;
    /**
     * @var integer
     */
    public $voteCount;
    /**
     * @var float
     */
    public $popularity;
    /**
     * @var string
     */
    public $overview;
}
