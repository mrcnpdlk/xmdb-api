<?php
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