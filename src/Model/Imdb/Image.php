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
 * Class Image
 *
 * @package mrcnpdlk\Xmdb\Model\Imdb
 */
class Image
{
    /**
     * @var integer
     */
    public $width;
    /**
     * @var integer
     */
    public $height;
    /**
     * @var string
     */
    public $url;

    /**
     * Image constructor.
     *
     * @param string   $url
     * @param int|null $width
     * @param int|null $height
     */
    public function __construct(string $url, int $width = null, int $height = null)
    {
        $this->width  = $width;
        $this->height = $height;
        $this->url    = $url;
    }
}
