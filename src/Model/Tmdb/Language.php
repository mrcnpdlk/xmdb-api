<?php
/**
 * Created by Marcin.
 * Date: 30.11.2017
 * Time: 00:13
 */

namespace mrcnpdlk\Xmdb\Model\Tmdb;


class Language
{
    /**
     * ISO 639 code
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
