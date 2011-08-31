<?php
/**
 * File contains: ezp\Content\Tests\LocationTest class
 *
 * @copyright Copyright (C) 1999-2011 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace ezp\User\Tests;
use ezp\Content\Tests\Service\Base as BaseServiceTest,
    ezp\User,
    ezp\User\Role,
    ezp\User\Policy,
    Exception;

/**
 * Test case for Location class
 *
 */
class ServiceTest extends BaseServiceTest
{
    /**
     * Test service function for creating user
     *
     * @covers \ezp\User\Service::create
     */
    public function testCreate()
    {
        $service = $this->repository->getUserService();
        $do = new User();
        $do->getState( 'properties' )->id = 1;
        $do->login = $do->password ='test';
        $do->email = 'test@ez.no';
        $do->getState( 'properties' )->hashAlgorithm = 2;
        $do = $service->create( $do );
        self::assertEquals( 1, $do->id );
        self::assertEquals( 'test', $do->login );
        self::assertEquals( 'test@ez.no', $do->email );
    }

    /**
     * Test service function for creating user
     *
     * @covers \ezp\User\Service::create
     * @expectedException \ezp\Base\Exception\Logic
     */
    public function testCreateExistingId()
    {
        $service = $this->repository->getUserService();
        $do = new User();
        $do->getState( 'properties' )->id = 14;
        $do->login = $do->password ='test';
        $do->email = 'test@ez.no';
        $do->getState( 'properties' )->hashAlgorithm = 2;
        $service->create( $do );
    }

    /**
     * Test service function for creating user
     *
     * @covers \ezp\User\Service::create
     * @expectedException \ezp\Base\Exception\PropertyNotFound
     */
    public function testCreateMissingId()
    {
        $service = $this->repository->getUserService();
        $do = new User();
        $do->login = $do->password ='test';
        $do->email = 'test@ez.no';
        $do->getState( 'properties' )->hashAlgorithm = 2;
        $service->create( $do );
    }

    /**
     * Test service function for loading user
     *
     * @covers \ezp\User\Service::load
     */
    public function testLoad()
    {
        $service = $this->repository->getUserService();
        $do = $service->load( 14 );
        self::assertEquals( 14, $do->id );
        self::assertEquals( 'admin', $do->login );
        self::assertEquals( 'spam@ez.no', $do->email );
    }

    /**
     * Test service function for loading user
     *
     * @covers \ezp\User\Service::load
     * @expectedException \ezp\Base\Exception\NotFound
     */
    public function testLoadNotFound()
    {
        $service = $this->repository->getUserService();
        $service->load( 999 );
    }

    /**
     * Test service function for updating user
     *
     * @covers \ezp\User\Service::update
     */
    public function testUpdate()
    {
        $service = $this->repository->getUserService();
        $do = $service->load( 14 );
        $do->login ='test';
        $service->update( $do );
        $do = $service->load( 14 );
        self::assertEquals( 14, $do->id );
        self::assertEquals( 'test', $do->login );
        self::assertEquals( 'spam@ez.no', $do->email );
    }

    /**
     * Test service function for updating user
     *
     * @covers \ezp\User\Service::update
     * @expectedException \ezp\Base\Exception\PropertyNotFound
     */
    public function testUpdateMissingId()
    {
        $service = $this->repository->getUserService();
        $do = $service->load( 14 );
        $do->login = null;
        $service->update( $do );
    }

    /**
     * Test service function for deleting user
     *
     * @covers \ezp\User\Service::delete
     * @expectedException \ezp\Base\Exception\NotFound
     */
    public function testDelete()
    {
        $service = $this->repository->getUserService();
        try
        {
            $service->delete( 14 );
        }
        catch ( Exception $e )
        {
            self::assertTrue( false, "Did not except any exceptions here, got: " . $e );
        }

        $service->load( 14 );
    }

    /**
     * Test service function for deleting user
     *
     * @covers \ezp\User\Service::delete
     * @expectedException \ezp\Base\Exception\NotFound
     */
    public function testDeleteNotFound()
    {
        $service = $this->repository->getUserService();
        $service->delete( 999 );
    }

    /**
     * Test service function for creating role
     *
     * @covers \ezp\User\Service::createRole
     */
    public function testCreateRole()
    {
        $service = $this->repository->getUserService();
        $do = $service->createRole( $this->getRole() );
        self::assertEquals( 'test', $do->name );
        self::assertEquals( array(), $do->groupIds );
        self::assertEquals( 3, count( $do->policies ) );
        self::assertEquals( 'user', $do->policies[2]->module );
        self::assertEquals( '*', $do->policies[2]->function );
        self::assertEquals( '*', $do->policies[2]->limitations );
        self::assertEquals( $do->id, $do->policies[2]->roleId );
    }

    /**
     * Test service function for loading role
     *
     * @covers \ezp\User\Service::loadRole
     */
    public function testLoadRole()
    {
        $service = $this->repository->getUserService();
        $do = $service->createRole( $this->getRole() );

        $role = $service->loadRole( $do->id );
        self::assertEquals( $do->id, $role->id );
        self::assertEquals( 'test', $do->name );
    }

    /**
     * Test service function for loading role
     *
     * @covers \ezp\User\Service::loadRole
     * @expectedException \ezp\Base\Exception\NotFound
     */
    public function testLoadRoleNotFound()
    {
        $service = $this->repository->getUserService();
        $service->loadRole( 999 );
    }

    /**
     * Test service function for loading roles
     *
     * @covers \ezp\User\Service::loadRolesByGroupId
     */
    public function testLoadRolesByGroupIdNotFound()
    {
        $service = $this->repository->getUserService();
        self::assertEquals( array(), $service->loadRolesByGroupId( 999 ) );
        self::assertEquals( array(), $service->loadRolesByGroupId( 14 ) );
        self::assertEquals( array(), $service->loadRolesByGroupId( 10 ) );
    }

    /**
     * Test service function for updating role
     *
     * @covers \ezp\User\Service::updateRole
     */
    public function testUpdateRole()
    {
        $service = $this->repository->getUserService();
        $do = $service->createRole( $this->getRole() );

        $do->name = 'updated';
        $service->updateRole( $do );

        $role = $service->loadRole( $do->id );
        self::assertEquals( $do->id, $role->id );
        self::assertEquals( 'updated', $do->name );
    }

    /**
     * Test service function for deleting role
     *
     * @covers \ezp\User\Service::delete
     * @expectedException \ezp\Base\Exception\NotFound
     */
    public function testDeleteRole()
    {
        $service = $this->repository->getUserService();
        try
        {
            $do = $service->createRole( $this->getRole() );
            $service->deleteRole( $do->id );
        }
        catch ( Exception $e )
        {
            self::assertTrue( false, "Did not except any exceptions here, got: " . $e );
        }

        $service->loadRole( $do->id );
    }

    /**
     * Test service function for deleting role
     *
     * @covers \ezp\User\Service::deleteRole
     * @expectedException \ezp\Base\Exception\NotFound
     */
    public function testDeleteRoleNotFound()
    {
        $service = $this->repository->getUserService();
        $service->deleteRole( 999 );
    }

    /**
     * Test service function for adding policy on a role
     *
     * @covers \ezp\User\Service::addPolicy
     */
    public function testAddPolicy()
    {
        $service = $this->repository->getUserService();
        $do = $service->createRole( $this->getRole() );

        $policy = new Policy( $do );
        $policy->module = 'Foo';
        $policy->function = 'Bar';
        $policy->limitations = array( 'Limit' => array( 'Test' ) );

        $service->addPolicy( $do, $policy );
        self::assertEquals( 4, count( $do->policies ) );
        self::assertEquals( 'Foo', $do->policies[3]->module );
        self::assertEquals( 'Bar', $do->policies[3]->function );
        self::assertEquals( array( 'Limit' => array( 'Test' ) ), $do->policies[3]->limitations );
        self::assertEquals( $do->id, $do->policies[3]->roleId );

        $do = $service->loadRole( $do->id );
        self::assertEquals( 4, count( $do->policies ) );
        self::assertEquals( 'Foo', $do->policies[3]->module );
        self::assertEquals( 'Bar', $do->policies[3]->function );
        self::assertEquals( array( 'Limit' => array( 'Test' ) ), $do->policies[3]->limitations );
        self::assertEquals( $do->id, $do->policies[3]->roleId );
    }

    /**
     * Test service function for removing policy on a role
     *
     * @covers \ezp\User\Service::removePolicy
     */
    public function testRemovePolicy()
    {
        $service = $this->repository->getUserService();
        $do = $service->createRole( $this->getRole() );

        $service->removePolicy( $do, $do->policies[2] );
        self::assertEquals( 2, count( $do->policies ) );
        self::assertEquals( 'content', $do->policies[1]->module );
        self::assertEquals( 'read', $do->policies[1]->function );
        self::assertEquals( '*', $do->policies[1]->limitations );
        self::assertEquals( $do->id, $do->policies[1]->roleId );

        $do = $service->loadRole( $do->id );
        self::assertEquals( 2, count( $do->policies ) );
        self::assertEquals( 'content', $do->policies[1]->module );
        self::assertEquals( 'read', $do->policies[1]->function );
        self::assertEquals( '*', $do->policies[1]->limitations );
        self::assertEquals( $do->id, $do->policies[1]->roleId );
    }

    /**
     * Test service function for loading policies by user id
     *
     * @covers \ezp\User\Service::loadPoliciesByUserId
     * @expectedException \ezp\Base\Exception\NotFound
     */
    public function testLoadPoliciesByUserIdNotFound()
    {
        $service = $this->repository->getUserService();
        self::assertEquals( array(), $service->loadPoliciesByUserId( 999 ) );
    }

    /**
     * @return \ezp\User\Role
     */
    protected function getRole()
    {
        $do = new Role();
        $do->name = 'test';

        $do->addPolicy( $policy = new Policy( $do ) );
        $policy->module = 'content';
        $policy->function = 'write';
        $policy->limitations = array( 'SubTree' => array( '/1/2/' ) );

        $do->addPolicy( $policy = new Policy( $do ) );
        $policy->module = 'content';
        $policy->function = 'read';
        $policy->limitations = '*';

        $do->addPolicy( $policy = new Policy( $do ) );
        $policy->module = 'user';
        $policy->function = $policy->limitations = '*';
        return $do;
    }
}