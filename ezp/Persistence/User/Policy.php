<?php
/**
 * File containing the Policy class
 *
 * @copyright Copyright (C) 1999-2011 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 * @package ezp
 * @subpackage persistence_user
 */

namespace ezp\Persistence\User;

/**
 * @package ezp
 * @subpackage persistence_user
 */
class Policy extends \ezp\Persistence\AbstractValueObject
{
    /**
     */
    public $module;
    /**
     */
    public $moduleFunction;
    /**
     */
    public $limitations;
    /**
     */
    public $unnamed_Role_;
}
?>
