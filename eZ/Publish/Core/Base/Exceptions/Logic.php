<?php
/**
 * Contains Logic Exception implementation
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\Core\Base\Exceptions;
use Exception,
    LogicException;

/**
 * Logic Exception implementation
 *
 * Use:
 *   throw new Logic( "readWriteProperties", "property {$property} could not be found." );
 *
 */
class Logic extends LogicException implements \ezp\Base\Exception
{
    /**
     * Generates: '$what' has a logic error[, $consequence]
     *
     * @param string $what
     * @param string|null $consequence Optional string to explain consequence of configuration mistake
     * @param \Exception|null $previous
     */
    public function __construct( $what, $consequence = null, Exception $previous = null )
    {
        if ( $consequence === null )
            parent::__construct( "'{$what}' has a logic error", 0, $previous );
        else
            parent::__construct( "'{$what}' has a logic error, {$consequence}", 0, $previous );
    }
}