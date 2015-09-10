<?php

/*
 * This file is part of the RollerworksSearch package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Search\Tests\Metadata\Fixtures\Entity;

use Rollerworks\Component\Search\Metadata\Field as SearchField;

class User
{
    /**
     * @SearchField("uid", type="integer")
     */
    public $id;

    /**
     * @SearchField("username", type="text", options={"name": "doctor", "last": {"who", "zeus"} })
     */
    public $name;
}
