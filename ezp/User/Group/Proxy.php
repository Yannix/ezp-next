<?php
/**
 * File containing Proxy Group class
 *
 * @copyright Copyright (C) 1999-2011 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace ezp\User\Group;
use ezp\Base\Proxy as BaseProxy,
    ezp\Base\ModelInterface,
    ezp\Base\Observable,
    ezp\Base\Observer,
    ezp\User\Group,
    ezp\User\Groupable,
    ezp\User\Service;

/**
 * This class represents a Proxy Group item
 *
 * Group is currently a facade for content objects of User Group type.
 * It requires that the User Group Content Type used has two attributes: name & description, both ezstring field types
 *
 * @property-read mixed $id
 * @property string $name
 * @property string description
 */
class Proxy extends BaseProxy implements Group, Groupable, ModelInterface, Observable
{
    public function __construct( $id, Service $service )
    {
        parent::__construct( $id, $service );
    }

    protected function lazyLoad()
    {
        if ( $this->proxiedObject === null )
        {
            $this->proxiedObject = $this->service->loadGroup( $this->proxiedObjectId );
        }
    }

    /**
     * @return \ezp\User\Group|null
     */
    public function getParent()
    {
        $this->lazyLoad();
        return $this->getParent();
    }

    /**
     * Roles assigned to Group
     *
     * Use {@link \ezp\User\Service::assignRole} & {@link \ezp\User\Service::unassignRole} to change
     *
     * @return \ezp\User\Role[]
     */
    public function getRoles()
    {
        $this->lazyLoad();
        return $this->getRoles();
    }

    /**
     * Return list of properties, where key is properties and value depends on type and is internal so should be ignored for now.
     *
     * @return array
     */
    public function properties()
    {
        $this->lazyLoad();
        return $this->properties();
    }

    /**
     * Attach a event listener to this subject
     *
     * @param \ezp\Base\Observer $observer
     * @param string $event
     * @return Model
     */
    public function attach( Observer $observer, $event = 'update' )
    {
        $this->lazyLoad();
        return $this->attach( $observer, $event );
    }

    /**
     * Detach a event listener to this subject
     *
     * @param \ezp\Base\Observer $observer
     * @param string $event
     * @return Model
     */
    public function detach( Observer $observer, $event = 'update' )
    {
        $this->lazyLoad();
        return $this->detach( $observer, $event );
    }

    /**
     * Notify listeners about certain events, by default $event is a plain 'update'
     *
     * @param string $event
     * @param array|null $arguments
     * @return Model
     */
    public function notify( $event = 'update', array $arguments = null )
    {
        $this->lazyLoad();
        return $this->notify( $event, $arguments );
    }

    /**
     * Sets internal variables on object from array
     *
     * Key is property name and value is property value.
     *
     * @access private
     * @param array $state
     * @return Model
     * @throws \ezp\Base\Exception\PropertyNotFound If one of the properties in $state is not found
     */
    public function setState( array $state )
    {
        $this->lazyLoad();
        return $this->setState( $state );
    }

    /**
     * Gets internal variables on object as array
     *
     * Key is property name and value is property value.
     *
     * @access private
     * @param string|null $property Optional, lets you specify to only return one property by name
     * @return array|mixed Always returns array if $property is null, else value of property
     * @throws \ezp\Base\Exception\PropertyNotFound If $property is not found (when not null)
     */
    public function getState( $property = null )
    {
        $this->lazyLoad();
        return $this->getState( $property );
    }
}