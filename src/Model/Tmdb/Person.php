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
 * Created by Marcin Pudełek <marcin@pudelek.org.pl>
 * Date: 30.11.2017
 * Time: 09:34
 */

namespace mrcnpdlk\Xmdb\Model\Tmdb;


class Person
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
     * @var integer
     */
    public $gender;
    /**
     * @var string
     */
    public $photo;

    public function __construct(int $id, string $name, int $gender = null, string $photo = null)
    {
        $this->id     = $id;
        $this->name   = $name;
        $this->gender = $gender;
        $this->photo  = $photo;
    }
}
