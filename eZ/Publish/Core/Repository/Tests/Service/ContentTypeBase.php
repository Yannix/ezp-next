<?php
/**
 * File contains: eZ\Publish\Core\Repository\Tests\Service\ContentTypeBase class
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\Core\Repository\Tests\Service;
use eZ\Publish\Core\Repository\Tests\Service\Base as BaseServiceTest,
    eZ\Publish\API\Repository\Values\Content\Location,
    eZ\Publish\API\Repository\Values\ContentType\ContentType,
    eZ\Publish\API\Repository\Exceptions;

/**
 * Test case for ContentType service
 */
abstract class ContentTypeBase extends BaseServiceTest
{
    /**
     * Test for the newContentTypeGroupCreateStruct() method.
     *
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::newContentTypeGroupCreateStruct()
     *
     * @return \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroupCreateStruct
     */
    public function testNewContentTypeGroupCreateStruct()
    {
        /* BEGIN: Use Case */
        $contentTypeService = $this->repository->getContentTypeService();

        $groupCreate = $contentTypeService->newContentTypeGroupCreateStruct(
            'new-group'
        );
        /* END: Use Case */

        $this->assertInstanceOf(
            'eZ\\Publish\\API\\Repository\\Values\\ContentType\\ContentTypeGroupCreateStruct',
            $groupCreate
        );
        return $groupCreate;
    }

    /**
     * Test for the newContentTypeGroupCreateStruct() method.
     *
     * @depends testNewContentTypeGroupCreateStruct
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::newContentTypeGroupCreateStruct()
     *
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroupCreateStruct $createStruct
     * @return void
     */
    public function testNewContentTypeGroupCreateStructValues( $createStruct )
    {
        $this->assertPropertiesCorrect(
            array(
                'identifier'       => 'new-group',
                'creatorId'        => null,
                'creationDate'     => null,
                'mainLanguageCode' => null,
                'names'            => null,
                'descriptions'     => null
            ),
            $createStruct
        );
    }

    /**
     * Test for the createContentTypeGroup() method.
     *
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::createContentTypeGroup()
     *
     * @return array
     */
    public function testCreateContentTypeGroup()
    {
        /* BEGIN: Use Case */
        $contentTypeService = $this->repository->getContentTypeService();

        $groupCreate = $contentTypeService->newContentTypeGroupCreateStruct(
            'new-group'
        );
        $groupCreate->creatorId        = 23;
        $groupCreate->creationDate     = new \DateTime();
        $groupCreate->mainLanguageCode = 'eng-GB';
        $groupCreate->names            = array( 'eng-US' => 'A name.' );
        $groupCreate->descriptions     = array( 'eng-US' => 'A description.' );

        $group = $contentTypeService->createContentTypeGroup( $groupCreate );
        /* END: Use Case */

        $this->assertInstanceOf(
            'eZ\\Publish\\API\\Repository\\Values\\ContentType\\ContentTypeGroup',
            $group
        );

        return array(
            'expected' => $groupCreate,
            'actual'   => $group,
        );
    }

    /**
     * Test for the newContentTypeGroupCreateStruct() method.
     *
     * @depends testCreateContentTypeGroup
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::newContentTypeGroupCreateStruct()
     *
     * @param array $data
     * @return void
     * @todo remove $notImplemented when implemented
     */
    public function testCreateContentTypeGroupStructValues( array $data )
    {
        $notImplemented = array(
            'names',
            'descriptions',
            'mainLanguageCode'
        );

        $this->assertStructPropertiesCorrect(
            $data['expected'],
            $data['actual'],
            $notImplemented
        );

        $this->assertNotNull(
            $data['actual']->id
        );
    }

    /**
     * Test for the createContentTypeGroup() method.
     *
     * @expectedException \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::createContentTypeGroup()
     *
     * @return void
     */
    public function testCreateContentTypeGroupThrowsUnauthorizedException()
    {
        $this->markTestIncomplete( "Test for ContentTypeService::createContentTypeGroup() is not implemented." );
    }

    /**
     * Test for the createContentTypeGroup() method.
     *
     * @expectedException \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::createContentTypeGroup()
     *
     * @return void
     * @todo remove setting creatorId when users are plugged in
     */
    public function testCreateContentTypeGroupThrowsInvalidArgumentException()
    {
        /* BEGIN: Use Case */
        $contentTypeService = $this->repository->getContentTypeService();

        $groupCreate = $contentTypeService->newContentTypeGroupCreateStruct(
            'new-group'
        );
        $groupCreate->creatorId = 23;
        $contentTypeService->createContentTypeGroup( $groupCreate );

        $secondGroupCreate = $contentTypeService->newContentTypeGroupCreateStruct(
            'new-group'
        );

        // Throws an exception because group with identifier "new-group" already exists
        $contentTypeService->createContentTypeGroup( $secondGroupCreate );
        /* END: Use Case */
    }

    /**
     * Test for the loadContentTypeGroup() method.
     *
     * @depends testCreateContentTypeGroup
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::loadContentTypeGroup()
     *
     * @return array
     */
    public function testLoadContentTypeGroup()
    {
        $contentTypeService = $this->repository->getContentTypeService();

        $groupCreate = $contentTypeService->newContentTypeGroupCreateStruct(
            'new-group'
        );
        $groupCreate->creatorId        = 23;
        $groupCreate->creationDate     = new \DateTime();
        $groupCreate->mainLanguageCode = 'eng-GB';
        $groupCreate->names            = array( 'eng-US' => 'A name.' );
        $groupCreate->descriptions     = array( 'eng-US' => 'A description.' );

        $storedGroup = $contentTypeService->createContentTypeGroup( $groupCreate );

        /* BEGIN: Use Case */
        $loadedGroup = $contentTypeService->loadContentTypeGroup(
            $storedGroup->id
        );
        /* END: Use Case */

        $this->assertInstanceOf(
            'eZ\\Publish\\API\\Repository\\Values\\ContentType\\ContentTypeGroup',
            $loadedGroup
        );

        return array(
            'expected' => $storedGroup,
            'actual'   => $loadedGroup,
        );
    }

    /**
     * Test for the loadContentTypeGroup() method.
     *
     * @depends testLoadContentTypeGroup
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::loadContentTypeGroup()
     *
     * @param array $data
     * @return void
     */
    public function testLoadContentTypeGroupValues( array $data )
    {
        $this->assertContentTypeGroupsEqual( $data );
    }

    /**
     * Test for the loadContentTypeGroup() method.
     *
     * @expectedException \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::loadContentTypeGroup()
     *
     * @return void
     */
    public function testLoadContentTypeGroupThrowsNotFoundException()
    {
        /* BEGIN: Use Case */
        $contentTypeService = $this->repository->getContentTypeService();

        // Throws exception
        $loadedGroup = $contentTypeService->loadContentTypeGroup( PHP_INT_MAX );
        /* END: Use Case */
    }

    /**
     * Test for the loadContentTypeGroupByIdentifier() method.
     *
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::loadContentTypeGroupByIdentifier()
     *
     * @return array
     */
    public function testLoadContentTypeGroupByIdentifier()
    {
        /* BEGIN: Use Case */
        $storedGroup = $this->createContentTypeGroup();
        $contentTypeService = $this->repository->getContentTypeService();

        $loadedGroup = $contentTypeService->loadContentTypeGroupByIdentifier(
            $storedGroup->identifier
        );
        /* END: Use Case */

        $this->assertInstanceOf(
            'eZ\\Publish\\API\\Repository\\Values\\ContentType\\ContentTypeGroup',
            $loadedGroup
        );

        return array(
            'expected' => $storedGroup,
            'actual'   => $loadedGroup,
    );
    }

    /**
     * Test for the loadContentTypeGroupByIdentifier() method.
     *
     * @depends testLoadContentTypeGroupByIdentifier
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::loadContentTypeGroupByIdentifier()
     *
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup[] $data
     *
     * @return void
     */
    public function testLoadContentTypeGroupByIdentifierValues( array $data )
    {
        $this->assertContentTypeGroupsEqual( $data );
    }

    /**
     * Asserts that two given ContentTypeGroup objects contain the same group data
     *
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup[] $data
     *
     * @return void
     *
     * @todo remove $notImplemented when implemented
     */
    protected function assertContentTypeGroupsEqual( array $data )
    {
        $storedGroup = $data['expected'];
        $loadedGroup = $data['actual'];
        $notImplemented = array(
            "mainLanguageCode",
            "names",
            "descriptions"
        );

        $this->assertSameClassPropertiesCorrect(
            array(
                "id",
                "identifier",
                "creationDate",
                "modificationDate",
                "creatorId",
                "modifierId",
                "mainLanguageCode",
                "names",
                "descriptions"
            ),
            $storedGroup,
            $loadedGroup,
            $notImplemented
        );
    }

    /**
     * Test for the loadContentTypeGroupByIdentifier() method.
     *
     * @expectedException \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::loadContentTypeGroupByIdentifier()
     *
     * @return void
     */
    public function testLoadContentTypeGroupByIdentifierThrowsNotFoundException()
    {
        /* BEGIN: Use Case */
        $contentTypeService = $this->repository->getContentTypeService();

        // Throws an exception
        $loadedGroup = $contentTypeService->loadContentTypeGroupByIdentifier(
            'the-no-identifier-like-this'
        );
        /* END: Use Case */
    }

    /**
     * Test for the loadContentTypeGroups() method.
     *
     * @depends testLoadContentTypeGroup
     *
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::loadContentTypeGroups()
     *
     * @return array
     */
    public function testLoadContentTypeGroups()
    {
        /* BEGIN: Use Case */
        $contentTypeService = $this->repository->getContentTypeService();

        $loadedGroups = $contentTypeService->loadContentTypeGroups();
        /* END: Use Case */

        $this->assertInternalType(
            'array',
            $loadedGroups
        );

        foreach ( $loadedGroups as $loadedGroup )
        {
            $this->assertContentTypeGroupsEqual(
                array(
                    "expected" => $contentTypeService->loadContentTypeGroup( $loadedGroup->id ),
                    "actual"   => $loadedGroup
                )
            );
        }

        return $loadedGroups;
    }

    /**
     * Test for the loadContentTypeGroups() method.
     *
     * @depends testLoadContentTypeGroups
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::loadContentTypeGroups()
     *
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup[] $groups
     * @return void
     */
    public function testLoadContentTypeGroupsIdentifiers( array $groups )
    {
        $expectedIdentifiers = array(
            'Content' => true,
            'Users'   => true,
            'Media'   => true,
            'Setup'   => true
        );

        $this->assertEquals(
            count( $expectedIdentifiers ),
            count( $groups )
        );

        $actualIdentifiers  = array(
            'Content' => false,
            'Users'   => false,
            'Media'   => false,
            'Setup'   => false
        );

        foreach ( $groups as $group )
        {
            $actualIdentifiers[$group->identifier] = true;
        }

        $this->assertEquals(
            $expectedIdentifiers,
            $actualIdentifiers,
            'Identifier mismatch in loaded and actual groups.'
        );
    }

    /**
     * Test for the newContentTypeGroupUpdateStruct() method.
     *
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::newContentTypeGroupUpdateStruct()
     *
     * @return void
     */
    public function testNewContentTypeGroupUpdateStruct()
    {
        /* BEGIN: Use Case */
        $contentTypeService = $this->repository->getContentTypeService();

        $groupUpdate = $contentTypeService->newContentTypeGroupUpdateStruct();
        /* END: Use Case */

        $this->assertInstanceOf(
            'eZ\\Publish\\API\\Repository\\Values\\ContentType\\ContentTypeGroupUpdateStruct',
            $groupUpdate
        );
    }

    /**
     * Test for the updateContentTypeGroup() method.
     *
     * @depends testLoadContentTypeGroup
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::updateContentTypeGroup()
     *
     * @return array
     */
    public function testUpdateContentTypeGroup()
    {
        $this->createContentTypeGroup();

        /* BEGIN: Use Case */
        $contentTypeService = $this->repository->getContentTypeService();

        $group = $contentTypeService->loadContentTypeGroupByIdentifier( 'new-group' );

        $groupUpdate = $contentTypeService->newContentTypeGroupUpdateStruct();
        $groupUpdate->identifier = 'updated-group';
        $groupUpdate->modifierId = 42;
        $groupUpdate->modificationDate = new \DateTime();
        $groupUpdate->mainLanguageCode = 'en_US';
        $groupUpdate->names = array(
            'en_US' => 'A name',
            'en_GB' => 'A name',
        );
        $groupUpdate->descriptions = array(
            'en_US' => 'A description',
            'en_GB' => 'A description',
        );

        $contentTypeService->updateContentTypeGroup( $group, $groupUpdate );
        /* END: Use Case */

        $updatedGroup = $contentTypeService->loadContentTypeGroup( $group->id );

        $this->assertInstanceOf(
            'eZ\\Publish\\API\\Repository\\Values\\ContentType\\ContentTypeGroupUpdateStruct',
            $groupUpdate
        );

        return array(
            'originalGroup' => $group,
            'updateStruct'  => $groupUpdate,
            'updatedGroup'  => $updatedGroup,
        );
    }

    /**
     * Test for the updateContentTypeGroup() method.
     *
     * @depends testUpdateContentTypeGroup
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::updateContentTypeGroup()
     *
     * @param array $data
     * @return void
     * @todo remove $notImplemented when implemented
     */
    public function testUpdateContentTypeGroupStructValues( array $data )
    {
        $originalGroup = $data['originalGroup'];
        $updateStruct = $data['updateStruct'];
        $updatedGroup = $data['updatedGroup'];

        $notImplemented = array(
            "mainLanguageCode",
            "names",
            "descriptions"
        );

        $expectedValues = array(
            'identifier'       => $updateStruct->identifier,
            'creationDate'     => $originalGroup->creationDate,
            'modificationDate' => $updateStruct->modificationDate,
            'creatorId'        => $originalGroup->creatorId,
            'modifierId'       => $updateStruct->modifierId,
            'mainLanguageCode' => $updateStruct->mainLanguageCode,
            'names'            => $updateStruct->names,
            'descriptions'     => $updateStruct->descriptions,
        );

        $this->assertPropertiesCorrect(
            $expectedValues,
            $updatedGroup,
            $notImplemented
        );
    }

    /**
     * Creates a group with identifier "new-group"
     *
     * @return \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup
     */
    protected function createContentTypeGroup()
    {
        $contentTypeService = $this->repository->getContentTypeService();

        $groupCreate = $contentTypeService->newContentTypeGroupCreateStruct(
            'new-group'
        );
        $groupCreate->creatorId        = 23;
        $groupCreate->creationDate     = new \DateTime();
        $groupCreate->mainLanguageCode = 'eng-US';
        $groupCreate->names            = array( 'eng-US' => 'Name' );
        $groupCreate->descriptions     = array( 'eng-US' => 'Description' );

        return $contentTypeService->createContentTypeGroup( $groupCreate );
    }

    /**
     * Test for the updateContentTypeGroup() method.
     *
     * @expectedException \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::updateContentTypeGroup()
     *
     * @return void
     */
    public function testUpdateContentTypeGroupThrowsUnauthorizedException()
    {
        $this->markTestIncomplete( "Test for ContentTypeService::updateContentTypeGroup() is not implemented." );
    }

    /**
     * Test for the updateContentTypeGroup() method.
     *
     * @depends testUpdateContentTypeGroup
     * @expectedException \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::updateContentTypeGroup()
     *
     * @return void
     */
    public function testUpdateContentTypeGroupThrowsInvalidArgumentException()
    {
        // Creates ContentTypeGroup with identifier "new-group"
        $this->createContentTypeGroup();

        /* BEGIN: Use Case */
        $contentTypeService = $this->repository->getContentTypeService();

        $groupCreate = $contentTypeService->newContentTypeGroupCreateStruct(
            'updated-group'
        );
        $groupCreate->creatorId = 23;
        $groupToOverwrite = $contentTypeService->createContentTypeGroup( $groupCreate );

        $group = $contentTypeService->loadContentTypeGroupByIdentifier( 'new-group' );

        $groupUpdate = $contentTypeService->newContentTypeGroupUpdateStruct();
        $groupUpdate->identifier = 'updated-group';

        // Exception, because group with identifier "updated-group" exists
        $contentTypeService->updateContentTypeGroup( $group, $groupUpdate );
        /* END: Use Case */
    }

    /**
     * Test for the deleteContentTypeGroup() method.
     *
     * @depends testCreateContentTypeGroup
     * @depends testLoadContentTypeGroup
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::deleteContentTypeGroup()
     *
     * @return void
     */
    public function testDeleteContentTypeGroup()
    {
        // Creates ContentTypeGroup with identifier "new-group"
        $this->createContentTypeGroup();

        /* BEGIN: Use Case */
        $contentTypeService = $this->repository->getContentTypeService();

        $group = $contentTypeService->loadContentTypeGroupByIdentifier( 'new-group' );

        $contentTypeService->deleteContentTypeGroup( $group );
        /* END: Use Case */

        try
        {
            $contentTypeService->loadContentTypeGroup( $group->id );
            $this->fail( 'Content type group not deleted.' );
        }
        catch ( Exceptions\NotFoundException $e )
        {
            // All fine
        }
    }

    /**
     * Test for the deleteContentTypeGroup() method.
     *
     * @expectedException \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::deleteContentTypeGroup()
     *
     * @return void
     */
    public function testDeleteContentTypeGroupThrowsInvalidArgumentException()
    {
        $this->createContentTypeGroup();

        /* BEGIN: Use Case */
        $contentTypeService = $this->repository->getContentTypeService();
        // "Content" group
        $contentGroup = $contentTypeService->loadContentTypeGroup( 1 );

        // Throws exception because group content type has instances
        $contentTypeService->deleteContentTypeGroup( $contentGroup );
        /* END: Use Case */
    }

    /**
     * Test for the deleteContentTypeGroup() method.
     *
     * @expectedException \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::deleteContentTypeGroup()
     *
     * @return void
     */
    public function testDeleteContentTypeGroupThrowsUnauthorizedException()
    {
        $this->markTestIncomplete( "Test for ContentTypeService::deleteContentTypeGroup() is not implemented." );
    }

    /**
     * Creates a number of ContentTypeGroup objects and returns them
     *
     * @return \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup[]
     */
    protected function createGroups()
    {
        $contentTypeService = $this->repository->getContentTypeService();

        $groups = array();

        $groupCreate = $contentTypeService->newContentTypeGroupCreateStruct(
            'first-group'
        );
        $groupCreate->creatorId        = 23;
        $groupCreate->creationDate     = new \DateTime();
        $groupCreate->mainLanguageCode = 'de_DE';
        $groupCreate->names            = array( 'en_US' => 'A name.' );
        $groupCreate->descriptions     = array( 'en_US' => 'A description.' );
        $groups[] = $contentTypeService->createContentTypeGroup( $groupCreate );

        $groupCreate->identifier = 'second-group';
        $groups[] = $contentTypeService->createContentTypeGroup( $groupCreate );

        return $groups;
    }

    /**
     * Test for the newContentTypeUpdateStruct() method.
     *
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::newContentTypeUpdateStruct()
     *
     * @return \eZ\Publish\API\Repository\Values\ContentType\ContentTypeUpdateStruct
     */
    public function testNewContentTypeUpdateStruct()
    {
        /* BEGIN: Use Case */
        $contentTypeService = $this->repository->getContentTypeService();

        $contentTypeUpdateStruct = $contentTypeService->newContentTypeUpdateStruct();
        /* END: Use Case */

        $this->assertInstanceOf(
            'eZ\\Publish\\API\\Repository\\Values\\ContentType\\ContentTypeUpdateStruct',
            $contentTypeUpdateStruct
        );

        return $contentTypeUpdateStruct;
    }

    /**
     * Test for the newContentTypeUpdateStruct() method.
     *
     * @depends testNewContentTypeUpdateStruct
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::newContentTypeUpdateStruct()
     *
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentTypeUpdateStruct $contentTypeUpdateStruct
     * @return void
     */
    public function testNewContentTypeUpdateStructValues( $contentTypeUpdateStruct )
    {
        foreach ( $contentTypeUpdateStruct as $propertyName => $propertyValue )
        {
            $this->assertNull(
                $propertyValue,
                "Property '$propertyName' is not null."
            );
        }
    }

    /**
     * Test for the newContentTypeCreateStruct() method.
     *
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::newContentTypeCreateStruct()
     *
     * @return \eZ\Publish\API\Repository\Values\ContentType\ContentTypeCreateStruct
     */
    public function testNewContentTypeCreateStruct()
    {
        /* BEGIN: Use Case */
        $contentTypeService = $this->repository->getContentTypeService();

        $contentTypeCreateStruct = $contentTypeService->newContentTypeCreateStruct(
            'new-type'
        );
        /* END: Use Case */

        $this->assertInstanceOf(
            'eZ\\Publish\\API\\Repository\\Values\\ContentType\\ContentTypeCreateStruct',
            $contentTypeCreateStruct
        );

        return $contentTypeCreateStruct;
    }

    /**
     * Test for the newContentTypeCreateStruct() method.
     *
     * @depends testNewContentTypeCreateStruct
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::newContentTypeCreateStruct()
     *
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentTypeCreateStruct $contentTypeCreateStruct
     * @return void
     */
    public function testNewContentTypeCreateStructValues( $contentTypeCreateStruct )
    {
        $this->assertPropertiesCorrect(
            array(
                'identifier'             => 'new-type',
                "mainLanguageCode"       => null,
                "remoteId"               => null,
                "urlAliasSchema"         => null,
                "nameSchema"             => null,
                "isContainer"            => false,
                "defaultSortField"       => Location::SORT_FIELD_PUBLISHED,
                "defaultSortOrder"       => Location::SORT_ORDER_DESC,
                "defaultAlwaysAvailable" => true,
                "names"                  => null,
                "descriptions"           => null,
                "creatorId"              => null,
                "creationDate"           => null,
                "fieldDefinitions"       => array()
            ),
            $contentTypeCreateStruct
        );
    }

    /**
     * Test for the newContentTypeCreateStruct() method.
     *
     * @expectedException \eZ\Publish\API\Repository\Exceptions\InvalidArgumentValue
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::newContentTypeCreateStruct()
     *
     * @return void
     */
    public function testNewContentTypeCreateStructThrowsInvalidArgumentValue()
    {
        $this->markTestIncomplete( "Test for ContentTypeService::newContentTypeCreateStruct() is not implemented." );
    }

    /**
     * Test for the newContentTypeCreateStruct() method.
     *
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::newFieldDefinitionCreateStruct()
     *
     * @return \eZ\Publish\API\Repository\Values\ContentType\FieldDefinitionCreateStruct
     */
    public function testNewFieldDefinitionCreateStruct()
    {
        /* BEGIN: Use Case */
        $contentTypeService = $this->repository->getContentTypeService();

        $fieldDefinitionCreateStruct = $contentTypeService->newFieldDefinitionCreateStruct(
            "new-identifier",
            "new-fieldtype-identifier"
        );
        /* END: Use Case */

        $this->assertInstanceOf(
            'eZ\\Publish\\API\\Repository\\Values\\ContentType\\FieldDefinitionCreateStruct',
            $fieldDefinitionCreateStruct
        );

        return $fieldDefinitionCreateStruct;
    }

    /**
     * Test for the newContentTypeCreateStruct() method.
     *
     * @depends testNewFieldDefinitionCreateStruct
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::newFieldDefinitionCreateStruct()
     *
     * @param \eZ\Publish\API\Repository\Values\ContentType\FieldDefinitionCreateStruct $fieldDefinitionCreateStruct
     * @return void
     */
    public function testNewFieldDefinitionCreateStructValues( $fieldDefinitionCreateStruct )
    {
        $this->assertPropertiesCorrect(
            array(
                "fieldTypeIdentifier" => "new-fieldtype-identifier",
                "identifier"          => "new-identifier",
                "names"               => null,
                "descriptions"        => null,
                "fieldGroup"          => null,
                "position"            => null,
                "isTranslatable"      => null,
                "isRequired"          => null,
                "isInfoCollector"     => null,
                "validators"          => null,
                "fieldSettings"       => null,
                "defaultValue"        => null,
                "isSearchable"        => null
            ),
            $fieldDefinitionCreateStruct
        );
    }

    /**
     * Test for the deleteContentTypeGroup() method.
     *
     * @expectedException \eZ\Publish\API\Repository\Exceptions\InvalidArgumentValue
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::newFieldDefinitionCreateStruct()
     *
     * @return void
     */
    public function testNewFieldDefinitionCreateStructThrowsInvalidArgumentValue()
    {
        $this->markTestIncomplete( "Test for ContentTypeService::newContentTypeCreateStruct() is not implemented." );
    }

    /**
     * Test for the newFieldDefinitionUpdateStruct() method.
     *
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::newFieldDefinitionUpdateStruct()
     *
     * @return \eZ\Publish\API\Repository\Values\ContentType\FieldDefinitionUpdateStruct
     */
    public function testNewFieldDefinitionUpdateStruct()
    {
        /* BEGIN: Use Case */
        $contentTypeService = $this->repository->getContentTypeService();

        $fieldDefinitionUpdateStruct = $contentTypeService->newFieldDefinitionUpdateStruct();
        /* END: Use Case */

        $this->assertInstanceOf(
            'eZ\\Publish\\API\\Repository\\Values\\ContentType\\FieldDefinitionUpdateStruct',
            $fieldDefinitionUpdateStruct
        );

        return $fieldDefinitionUpdateStruct;
    }

    /**
     * Test for the newFieldDefinitionUpdateStruct() method.
     *
     * @depends testNewFieldDefinitionUpdateStruct
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::newContentTypeUpdateStruct()
     *
     * @param \eZ\Publish\API\Repository\Values\ContentType\FieldDefinitionUpdateStruct $fieldDefinitionUpdateStruct
     * @return void
     */
    public function testNewFieldDefinitionUpdateStructValues( $fieldDefinitionUpdateStruct )
    {
        foreach ( $fieldDefinitionUpdateStruct as $propertyName => $propertyValue )
        {
            $this->assertNull(
                $propertyValue,
                "Property '$propertyName' is not null."
            );
        }
    }

    /**
     * Creates a ContentType with identifier "new-type" and remoteId "new-remoteid"
     *
     * @param bool $publish
     * @return \eZ\Publish\API\Repository\Values\ContentType\ContentType
     */
    protected function createContentType( $publish = true )
    {
        $contentTypeService = $this->repository->getContentTypeService();

        $typeCreateStruct = $contentTypeService->newContentTypeCreateStruct(
            'new-type'
        );
        $typeCreateStruct->names = array(
            'eng-US' => 'American type title',
            'eng-GB' => 'British type title'
        );
        $typeCreateStruct->descriptions = array(
            'eng-US' => 'American type description',
            'eng-GB' => 'British type description'
        );
        $typeCreateStruct->remoteId             = "new-remoteid";
        $typeCreateStruct->creatorId            = 23;
        $typeCreateStruct->creationDate         = new \DateTime();
        $typeCreateStruct->mainLanguageCode     = 'eng-GB';
        $typeCreateStruct->nameSchema           = "<title>";
        $typeCreateStruct->urlAliasSchema       = "<title>";

        $titleFieldCreate = $contentTypeService->newFieldDefinitionCreateStruct(
            'title',
            'ezstring'
        );
        $titleFieldCreate->names = array(
            'eng-US' => 'American title field name',
            'eng-GB' => 'British title field name',
        );
        $titleFieldCreate->descriptions = array(
            'eng-US' => 'American title field description',
            'eng-GB' => 'British title field description',
        );
        $titleFieldCreate->fieldGroup      = 'blog-content';
        $titleFieldCreate->position        = 1;
        $titleFieldCreate->isTranslatable  = true;
        $titleFieldCreate->isRequired      = true;
        $titleFieldCreate->isInfoCollector = false;
        $titleFieldCreate->isSearchable    = true;
        $titleFieldCreate->defaultValue    = new \eZ\Publish\SPI\Persistence\Content\FieldValue(
            array(
                "data" => new \ezp\Content\FieldType\TextLine\Value( 'New text line' )
            )
        );
        //$titleFieldCreate->validators
        //$titleFieldCreate->fieldSettings
        $typeCreateStruct->addFieldDefinition( $titleFieldCreate );

        $bodyFieldCreate = $contentTypeService->newFieldDefinitionCreateStruct(
            'body',
            'eztext'
        );
        $bodyFieldCreate->names = array(
            'eng-US' => 'American body field name',
            'eng-GB' => 'British body field name',
        );
        $bodyFieldCreate->descriptions = array(
            'eng-US' => 'American body field description',
            'eng-GB' => 'British body field description',
        );
        $bodyFieldCreate->fieldGroup      = 'blog-content';
        $bodyFieldCreate->position        = 2;
        $bodyFieldCreate->isTranslatable  = true;
        $bodyFieldCreate->isRequired      = false;
        $bodyFieldCreate->isInfoCollector = false;
        $bodyFieldCreate->isSearchable    = true;
        $bodyFieldCreate->defaultValue    = new \eZ\Publish\SPI\Persistence\Content\FieldValue(
            array(
                "data" => new \ezp\Content\FieldType\TextBlock\Value( '' )
            )
        );
        //$bodyFieldCreate->validators
        $bodyFieldCreate->fieldSettings = array(
            "textColumns" => 80
        );
        $typeCreateStruct->addFieldDefinition( $bodyFieldCreate );

        $groups = $this->createGroups();

        $type = $contentTypeService->createContentType(
            $typeCreateStruct,
            $groups
        );

        if ( $publish !== true )
        {
            return $type;
        }

        $contentTypeService->publishContentTypeDraft( $type );

        return $contentTypeService->loadContentType( $type->id );
    }

    /**
     * Creates a ContentTypeDraft with identifier "new-type" and remoteId "new-remoteid"
     *
     * @return \eZ\Publish\API\Repository\Values\ContentType\ContentTypeDraft
     */
    protected function createDraftContentType()
    {
        return $this->createContentType( false );
    }

    /**
     * Creates a ContentType with identifier "new-type" and remoteId "new-remoteid"
     *
     * @return \eZ\Publish\API\Repository\Values\ContentType\ContentType
     */
    protected function createPublishedContentType()
    {
        return $this->createContentType( true );
    }

    /**
     * Test for the createContentTypeGroup() method.
     *
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::createContentTypeGroup()
     *
     * @return array
     */
    public function testCreateContentType()
    {
        /* BEGIN: Use Case */
        $contentTypeService = $this->repository->getContentTypeService();
        $typeCreateStruct = $contentTypeService->newContentTypeCreateStruct(
            'new-type'
        );
        $typeCreateStruct->names = array(
            'eng-US' => 'American type title',
            'eng-GB' => 'British type title'
        );
        $typeCreateStruct->descriptions = array(
            'eng-US' => 'American type description',
            'eng-GB' => 'British type description'
        );
        $typeCreateStruct->remoteId             = "new-remoteid";
        $typeCreateStruct->creatorId            = 23;
        $typeCreateStruct->creationDate         = new \DateTime();
        $typeCreateStruct->mainLanguageCode     = 'eng-GB';
        $typeCreateStruct->nameSchema           = "<name>";
        $typeCreateStruct->urlAliasSchema       = "<name>";

        $titleFieldCreate = $contentTypeService->newFieldDefinitionCreateStruct(
            'title',
            'ezstring'
        );
        $titleFieldCreate->names = array(
            'eng-US' => 'American title field name',
            'eng-GB' => 'British title field name',
        );
        $titleFieldCreate->descriptions = array(
            'eng-US' => 'American title field description',
            'eng-GB' => 'British title field description',
        );
        $titleFieldCreate->fieldGroup      = 'blog-content';
        $titleFieldCreate->position        = 1;
        $titleFieldCreate->isTranslatable  = true;
        $titleFieldCreate->isRequired      = true;
        $titleFieldCreate->isInfoCollector = false;
        $titleFieldCreate->isSearchable    = true;
        $titleFieldCreate->defaultValue    = new \eZ\Publish\SPI\Persistence\Content\FieldValue(
            array(
                "data" => new \ezp\Content\FieldType\TextLine\Value( 'New text line' )
            )
        );
        //$titleFieldCreate->validators
        //$titleFieldCreate->fieldSettings
        $typeCreateStruct->addFieldDefinition( $titleFieldCreate );

        $bodyFieldCreate = $contentTypeService->newFieldDefinitionCreateStruct(
            'body',
            'eztext'
        );
        $bodyFieldCreate->names = array(
            'eng-US' => 'American body field name',
            'eng-GB' => 'British body field name',
        );
        $bodyFieldCreate->descriptions = array(
            'eng-US' => 'American body field description',
            'eng-GB' => 'British body field description',
        );
        $bodyFieldCreate->fieldGroup      = 'blog-content';
        $bodyFieldCreate->position        = 2;
        $bodyFieldCreate->isTranslatable  = true;
        $bodyFieldCreate->isRequired      = false;
        $bodyFieldCreate->isInfoCollector = false;
        $bodyFieldCreate->isSearchable    = true;
        $bodyFieldCreate->defaultValue   = new \eZ\Publish\SPI\Persistence\Content\FieldValue(
            array(
                "data" => new \ezp\Content\FieldType\TextBlock\Value( '' )
            )
        );
        //$bodyFieldCreate->validators
        //$bodyFieldCreate->fieldSettings
        $typeCreateStruct->addFieldDefinition( $bodyFieldCreate );

        $groups = $this->createGroups();

        $type = $contentTypeService->createContentType(
            $typeCreateStruct,
            $groups
        );
        /* END: Use Case */

        $this->assertInstanceOf(
            'eZ\\Publish\\API\\Repository\\Values\\ContentType\\ContentTypeDraft',
            $type
        );

        return array(
            'expected' => $typeCreateStruct,
            'actual'   => $type,
            'groups'   => $groups
        );
    }

    /**
     * Test for the newContentTypeGroupCreateStruct() method.
     *
     * @depends testCreateContentType
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::newContentTypeGroupCreateStruct()
     *
     * @param array $data
     * @return void
     */
    public function testCreateContentTypeStructValues( array $data )
    {
        /** @var $typeCreate \eZ\Publish\API\Repository\Values\ContentType\ContentTypeCreateStruct */
        $typeCreate = $data['expected'];
        /** @var $contentType \eZ\Publish\Core\Repository\Values\ContentType\ContentType */
        $contentType = $data['actual'];
        /** @var $groups \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup[] */
        $groups = $data['groups'];

        foreach ( $typeCreate as $propertyName => $propertyValue )
        {
            switch ( $propertyName )
            {
                case 'fieldDefinitions':
                    $this->assertFieldDefinitionsCreatesCorrect(
                        $typeCreate->fieldDefinitions,
                        $contentType->fieldDefinitions
                    );
                    break;

                case 'contentTypeGroups':
                    $this->assertContentTypeGroupsCorrect(
                        $groups,
                        $contentType->contentTypeGroups
                    );
                    break;

                default:
                    $this->assertEquals(
                        $typeCreate->$propertyName,
                        $contentType->$propertyName
                    );
                    break;
            }
        }

        $this->assertContentTypeGroupsCorrect(
            $groups,
            $contentType->contentTypeGroups
        );

        $this->assertNotNull(
            $contentType->id
        );
    }

    /**
     * Asserts field definition creation
     *
     * Asserts that all field definitions defined through created structs in
     * $expectedDefinitionCreates have been correctly created in
     * $actualDefinitions.
     *
     * @param \eZ\Publish\API\Repository\Values\ContentType\FieldDefinitionCreateStruct[] $expectedDefinitionCreates
     * @param \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition[] $actualDefinitions
     *
     * @return void
     */
    protected function assertFieldDefinitionsCreatesCorrect( array $expectedDefinitionCreates, array $actualDefinitions )
    {
        $this->assertEquals(
            count( $expectedDefinitionCreates ),
            count( $actualDefinitions ),
            'Count of field definition creates did not match count of field definitions.'
        );

        $sorter = function( $a, $b )
        {
            return strcmp( $a->identifier, $b->identifier );
        };

        usort( $expectedDefinitionCreates, $sorter );
        usort( $actualDefinitions, $sorter );

        foreach ( $expectedDefinitionCreates as $key => $expectedCreate )
        {
            $this->assertInstanceOf(
                'eZ\\Publish\\API\\Repository\\Values\\ContentType\\FieldDefinition',
                $actualDefinitions[$key]
            );
            $this->assertFieldDefinitionsCreatesEqual(
                $expectedCreate,
                $actualDefinitions[$key]
            );
        }
    }

    /**
     * Asserts that a field definition has been correctly created.
     *
     * Asserts that the given $actualDefinition is correctly created from the
     * create struct in $expectedDefinitionCreate.
     *
     * @param \eZ\Publish\API\Repository\Values\ContentType\FieldDefinitionCreateStruct $expectedDefinitionCreate
     * @param \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition $actualDefinition
     *
     * @return void
     */
    protected function assertFieldDefinitionsCreatesEqual( $expectedDefinitionCreate, $actualDefinition )
    {
        foreach ( $expectedDefinitionCreate as $propertyName => $propertyValue )
        {
            $this->assertEquals(
                $propertyValue,
                $actualDefinition->$propertyName
            );
        }
    }

    /**
     * Asserts that two sets of ContentTypeGroups are equal.
     *
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup[] $expectedGroups
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup[] $actualGroups
     *
     * @return void
     */
    protected function assertContentTypeGroupsCorrect( array $expectedGroups, array $actualGroups )
    {
        $sorter = function ( $a, $b )
        {
            if ( $a->id == $b->id )
            {
                return 0;
            }
            return ( $a->id < $b->id ) ? -1 : 1;
        };

        usort( $expectedGroups, $sorter );
        usort( $actualGroups, $sorter );

        foreach ( $expectedGroups as $index => $expectedGroup )
        {
            $this->assertContentTypeGroupsEqual(
                array(
                     "expected" => $expectedGroup,
                     "actual" => $actualGroups[$index]
                )
            );
        }
    }

    /**
     * Test for the createContentTypeGroup() method.
     *
     * @depends testCreateContentType
     * @expectedException \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::createContentTypeGroup()
     *
     * @return void
     */
    public function testCreateContentTypeThrowsInvalidArgumentExceptionGroupsEmpty()
    {
        /* BEGIN: Use Case */
        $contentTypeService = $this->repository->getContentTypeService();

        $typeCreateStruct = $contentTypeService->newContentTypeCreateStruct(
            'new-type'
        );

        // Thrown an exception because array of content type groups is empty
        $type = $contentTypeService->createContentType( $typeCreateStruct, array() );
        /* END: Use Case */
    }

    /**
     * Test for the createContentTypeGroup() method.
     *
     * @depends testCreateContentType
     * @expectedException \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::createContentTypeGroup()
     *
     * @return void
     */
    public function testCreateContentTypeThrowsInvalidArgumentExceptionContentTypeExistsWithIdentifier()
    {
        /* BEGIN: Use Case */
        $contentTypeService = $this->repository->getContentTypeService();

        // Creates published content type with identifier "new-type"
        $this->createPublishedContentType();

        $typeCreateStruct = $contentTypeService->newContentTypeCreateStruct(
            'new-type'
        );
        $typeCreateStruct->remoteId             = "other-remoteid";
        $typeCreateStruct->creatorId            = 23;
        $typeCreateStruct->creationDate         = new \DateTime();
        $typeCreateStruct->mainLanguageCode     = 'eng-GB';
        $typeCreateStruct->names                = array('eng-US' => 'A name.');
        $typeCreateStruct->descriptions         = array('eng-US' => 'A description.');

        // Throws an exception because content type with identifier "new-type" already exists
        $type = $contentTypeService->createContentType(
            $typeCreateStruct,
            array(
                // "Content" group
                $contentTypeService->loadContentTypeGroup( 1 )
            )
        );
        /* END: Use Case */
    }

    /**
     * Test for the createContentTypeGroup() method.
     *
     * @depends testCreateContentType
     * @expectedException \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::createContentTypeGroup()
     *
     * @return void
     */
    public function testCreateContentTypeThrowsInvalidArgumentExceptionContentTypeExistsWithRemoteId()
    {
        /* BEGIN: Use Case */
        $contentTypeService = $this->repository->getContentTypeService();

        // Creates published content type with remoteId "new-remoteid"
        $this->createPublishedContentType();
        $typeCreateStruct = $contentTypeService->newContentTypeCreateStruct(
            'other-type'
        );
        $typeCreateStruct->remoteId             = "new-remoteid";
        $typeCreateStruct->creatorId            = 23;
        $typeCreateStruct->creationDate         = new \DateTime();
        $typeCreateStruct->mainLanguageCode     = 'eng-GB';
        $typeCreateStruct->names                = array('eng-US' => 'A name.');
        $typeCreateStruct->descriptions         = array('eng-US' => 'A description.');

        // Throws an exception because content type with remoteId "new-remoteid" already exists
        $type = $contentTypeService->createContentType(
            $typeCreateStruct,
            array(
                // "Content" group
                $contentTypeService->loadContentTypeGroup( 1 )
            )
        );
        /* END: Use Case */
    }

    /**
     * Test for the loadContentType() method.
     *
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::loadContentType()
     *
     * @return array
     */
    public function testLoadContentType()
    {
        $storedContentType = $this->createPublishedContentType();

        /* BEGIN: Use Case */
        $contentTypeService = $this->repository->getContentTypeService();

        $loadedContentType = $contentTypeService->loadContentType(
            $storedContentType->id
        );
        /* END: Use Case */

        $this->assertInstanceOf(
            'eZ\\Publish\\API\\Repository\\Values\\ContentType\\ContentType',
            $loadedContentType
        );

        $this->assertNotInstanceOf(
            'eZ\\Publish\\API\\Repository\\Values\\ContentType\\ContentTypeDraft',
            $loadedContentType
        );

        return array(
            'expected' => $storedContentType,
            'actual'   => $loadedContentType,
        );
    }

    /**
     * Test for the loadContentType() method.
     *
     * @depends testLoadContentType
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::loadContentType()
     *
     * @param array $data
     * @return void
     */
    public function testLoadContentTypeValues( array $data )
    {
        $this->assertLoadContentTypeValues( $data );
    }

    /**
     * Asserts that two given ContentType objects contain the same content type data
     *
     * @param array $data
     * @param array $skipProperties
     *
     * @return void
     */
    protected function assertLoadContentTypeValues( array $data, array $skipProperties = array(), array $skipFieldProperties = array() )
    {
        /** @var $storedContentType \eZ\Publish\Core\Repository\Values\ContentType\ContentType */
        $storedContentType = $data['expected'];
        /** @var $loadedContentType \eZ\Publish\Core\Repository\Values\ContentType\ContentType */
        $loadedContentType = $data['actual'];

        $this->assertSameClassPropertiesCorrect(
            array(
                 // Virtual properties
                 "names",
                 "descriptions",
                 "contentTypeGroups",
                 //"fieldDefinitions",
                 // Standard properties
                 "id",
                 "status",
                 "identifier",
                 "creationDate",
                 "modificationDate",
                 "creatorId",
                 "modifierId",
                 "remoteId",
                 "urlAliasSchema",
                 "nameSchema",
                 "isContainer",
                 "mainLanguageCode",
                 "defaultAlwaysAvailable",
                 "defaultSortField",
                 "defaultSortOrder"
            ),
            $storedContentType,
            $loadedContentType,
            $skipProperties
        );

        $this->assertEquals(
            count( $storedContentType->fieldDefinitions ),
            count( $loadedContentType->fieldDefinitions ),
            "Field count in stored and loaded content type groups does not match"
        );

        foreach ( $storedContentType->fieldDefinitions as $index => $expectedFieldDefinition )
        {
            $actualFieldDefinition = $loadedContentType->fieldDefinitions[$index];
            $this->assertInstanceOf(
                'eZ\\Publish\\API\\Repository\\Values\\ContentType\\FieldDefinition',
                $actualFieldDefinition
            );
            $this->assertFieldDefinitionsEqual(
                $expectedFieldDefinition,
                $actualFieldDefinition,
                $skipFieldProperties
            );
        }
    }

    /**
     * Asserts that two FieldDefinition objects contain the same field data
     *
     * @param \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition $expectedFieldDefinition
     * @param \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition $actualFieldDefinition
     * @param array $skip Array of field names to skip, used with createContentTypeDraft()
     *
     * @return void
     */
    protected function assertFieldDefinitionsEqual( $expectedFieldDefinition, $actualFieldDefinition, $skip = array() )
    {
        $this->assertSameClassPropertiesCorrect(
            array(
                 "names",
                 "descriptions",
                 "fieldSettings",
                 "validators",
                 "id",
                 "identifier",
                 "fieldGroup",
                 "position",
                 "fieldTypeIdentifier",
                 "isTranslatable",
                 "isRequired",
                 "isInfoCollector",
                 "defaultValue",
                 "isSearchable"
            ),
            $expectedFieldDefinition,
            $actualFieldDefinition,
            $skip
        );
    }

    /**
     * Test for the loadContentType() method.
     *
     * @depends testLoadContentType
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::loadContentType()
     *
     * @return void
     */
    public function testLoadContentTypeThrowsInvalidArgumentValue()
    {
        $this->markTestIncomplete( "Test for ContentTypeService::loadContentType() is not implemented." );
    }

    /**
     * Test for the loadContentType() method.
     *
     * @expectedException \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::loadContentType()
     *
     * @return void
     */
    public function testLoadContentTypeThrowsNotFoundException()
    {
        /* BEGIN: Use Case */
        $contentTypeService = $this->repository->getContentTypeService();

        $loadedContentType = $contentTypeService->loadContentType(
            PHP_INT_MAX
        );
        /* END: Use Case */
    }

    /**
     * Test for the loadContentTypeByIdentifier() method.
     *
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::loadContentTypeByIdentifier()
     *
     * @return array
     */
    public function testLoadContentTypeByIdentifier()
    {
        $storedContentType = $this->createPublishedContentType();

        /* BEGIN: Use Case */
        $contentTypeService = $this->repository->getContentTypeService();

        $loadedContentType = $contentTypeService->loadContentTypeByIdentifier(
            $storedContentType->identifier
        );
        /* END: Use Case */

        $this->assertInstanceOf(
            'eZ\\Publish\\API\\Repository\\Values\\ContentType\\ContentType',
            $loadedContentType
        );

        return array(
            'expected' => $storedContentType,
            'actual'   => $loadedContentType,
        );
    }

    /**
     * Test for the loadContentTypeByIdentifier() method.
     *
     * @depends testLoadContentTypeByIdentifier
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::loadContentTypeByIdentifier()
     *
     * @param array $data
     * @return void
     */
    public function testLoadContentTypeByIdentifierValues( array $data )
    {
        $this->assertLoadContentTypeValues( $data );
    }

    /**
     * Test for the loadContentTypeByIdentifier() method.
     *
     * @depends testLoadContentTypeByIdentifier
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::loadContentTypeByIdentifier()
     *
     * @return void
     */
    public function testLoadContentTypeByIdentifierThrowsInvalidArgumentValue()
    {
        $this->markTestIncomplete( "Test for ContentTypeService::loadContentTypeByIdentifier() is not implemented." );
    }

    /**
     * Test for the loadContentTypeByIdentifier() method.
     *
     * @expectedException \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::loadContentTypeByIdentifier()
     *
     * @return void
     */
    public function testLoadContentTypeByIdentifierThrowsNotFoundException()
    {
        /* BEGIN: Use Case */
        $contentTypeService = $this->repository->getContentTypeService();

        $loadedContentType = $contentTypeService->loadContentTypeByIdentifier(
            "non-existing-identifier"
        );
        /* END: Use Case */
    }

    /**
     * Test for the loadContentTypeByRemoteId() method.
     *
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::loadContentTypeByRemoteId()
     *
     * @return array
     */
    public function testLoadContentTypeByRemoteId()
    {
        $storedContentType = $this->createPublishedContentType();

        /* BEGIN: Use Case */
        $contentTypeService = $this->repository->getContentTypeService();

        $loadedContentType = $contentTypeService->loadContentTypeByRemoteId(
            $storedContentType->remoteId
        );
        /* END: Use Case */

        $this->assertInstanceOf(
            'eZ\\Publish\\API\\Repository\\Values\\ContentType\\ContentType',
            $loadedContentType
        );

        return array(
            'expected' => $storedContentType,
            'actual'   => $loadedContentType
        );
    }

    /**
     * Test for the loadContentTypeByRemoteId() method.
     *
     * @depends testLoadContentTypeByRemoteId
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::loadContentTypeByRemoteId()
     *
     * @param array $data
     * @return void
     */
    public function testLoadContentTypeByRemoteIdValues( array $data )
    {
        $this->assertLoadContentTypeValues( $data );
    }

    /**
     * Test for the loadContentTypeByRemoteId() method.
     *
     * @expectedException \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::loadContentTypeByRemoteId()
     *
     * @return void
     */
    public function testLoadContentTypeByRemoteIdThrowsNotFoundException()
    {
        /* BEGIN: Use Case */
        $contentTypeService = $this->repository->getContentTypeService();

        $loadedContentType = $contentTypeService->loadContentTypeByRemoteId(
            "non-existing-remoteid"
        );
        /* END: Use Case */
    }

    /**
     * Test for the loadContentTypeDraft() method.
     *
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::loadContentTypeDraft()
     *
     * @return array
     */
    public function testLoadContentTypeDraft()
    {
        $storedContentTypeDraft = $this->createDraftContentType();

        /* BEGIN: Use Case */
        $contentTypeService = $this->repository->getContentTypeService();

        $loadedContentTypeDraft = $contentTypeService->loadContentTypeDraft(
            $storedContentTypeDraft->id
        );
        /* END: Use Case */

        $this->assertInstanceOf(
            'eZ\\Publish\\API\\Repository\\Values\\ContentType\\ContentTypeDraft',
            $loadedContentTypeDraft
        );

        return array(
            'expected' => $storedContentTypeDraft,
            'actual'   => $loadedContentTypeDraft,
        );
    }

    /**
     * Test for the loadContentTypeDraft() method.
     *
     * @depends testLoadContentTypeDraft
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::loadContentTypeDraft()
     *
     * @param array $data
     * @return array
     */
    public function testLoadContentTypeDraftValues( array $data )
    {
        $this->assertLoadContentTypeValues( $data );
    }

    /**
     * Test for the loadContentTypeDraft() method.
     *
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::loadContentTypeDraft()
     *
     * @return array
     */
    public function testLoadContentTypeDraftThrowsInvalidArgumentValue()
    {
        $this->markTestIncomplete( "Test for ContentTypeService::loadContentTypeByIdentifier() is not implemented." );
    }

    /**
     * Test for the loadContentTypeDraft() method.
     *
     * @expectedException \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::loadContentTypeDraft()
     *
     * @return array
     */
    public function testLoadContentTypeDraftThrowsNotFoundException()
    {
        /* BEGIN: Use Case */
        $contentTypeService = $this->repository->getContentTypeService();

        $loadedContentType = $contentTypeService->loadContentTypeDraft(
            PHP_INT_MAX
        );
        /* END: Use Case */
    }

    /**
     * Test for the publishContentTypeDraft() method.
     *
     * @depends testCreateContentType
     * @depends testLoadContentTypeDraft
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::publishContentTypeDraft()
     *
     * @return void
     */
    public function testPublishContentTypeDraft()
    {
        $contentTypeDraft = $this->createDraftContentType();
        $draftId = $contentTypeDraft->id;

        /* BEGIN: Use Case */
        // $contentTypeDraft contains a ContentTypeDraft
        $contentTypeService = $this->repository->getContentTypeService();
        $contentTypeDraft = $contentTypeService->loadContentTypeDraft( $draftId );

        $contentTypeService->publishContentTypeDraft( $contentTypeDraft );
        /* END: Use Case */

        $publishedType = $contentTypeService->loadContentType( $draftId );

        $this->assertInstanceOf(
            'eZ\\Publish\\API\\Repository\\Values\\ContentType\\ContentType',
            $publishedType
        );
        $this->assertNotInstanceOf(
            'eZ\\Publish\\API\\Repository\\Values\\ContentType\\ContentTypeDraft',
            $publishedType
        );
    }

    /**
     * Test for the publishContentTypeDraft() method.
     *
     * @depends testPublishContentTypeDraft
     * @expectedException \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::publishContentTypeDraft()
     *
     * @return void
     */
    public function testPublishContentTypeDraftThrowsBadStateException()
    {
        $contentTypeDraft = $this->createDraftContentType();
        $draftId = $contentTypeDraft->id;

        /* BEGIN: Use Case */
        // $contentTypeDraft contains a ContentTypeDraft
        $contentTypeService = $this->repository->getContentTypeService();
        $contentTypeDraft = $contentTypeService->loadContentTypeDraft( $draftId );

        $contentTypeService->publishContentTypeDraft( $contentTypeDraft );

        // Throws exception, since no draft exists anymore
        $contentTypeService->publishContentTypeDraft( $contentTypeDraft );
        /* END: Use Case */
    }

    /**
     * Test for the publishContentTypeDraft() method.
     *
     * @depends testPublishContentTypeDraft
     * @expectedException \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::publishContentTypeDraft()
     *
     * @return void
     */
    public function testPublishContentTypeDraftThrowsUnauthorizedException()
    {
        $this->markTestIncomplete( "Test for ContentTypeService::publishContentTypeDraft() is not implemented." );
    }

    /**
     * Test for the loadContentTypes() method.
     *
     * @depends testNewContentTypeGroupCreateStruct
     * @depends testNewContentTypeCreateStruct
     * @depends testNewFieldDefinitionCreateStruct
     * @depends testCreateContentTypeGroup
     * @depends testCreateContentType
     * @depends testPublishContentTypeDraft
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::loadContentTypes()
     *
     * @todo when all fieldTypes are functional revisit this and simplify by testing against fixtures
     *
     * @return array
     */
    public function testLoadContentTypes()
    {
        $contentTypeService = $this->repository->getContentTypeService();

        $groupCreate = $contentTypeService->newContentTypeGroupCreateStruct(
            'test-group-1'
        );
        $groupCreate->creatorId        = 23;
        $groupCreate->creationDate     = new \DateTime();
        $groupCreate->mainLanguageCode = 'de_DE';
        $groupCreate->names            = array( 'en_US' => 'A name.' );
        $groupCreate->descriptions     = array( 'en_US' => 'A description.' );
        $group = $contentTypeService->createContentTypeGroup( $groupCreate );

        $typeCreateStruct = $contentTypeService->newContentTypeCreateStruct(
            'test-type-1'
        );
        $typeCreateStruct->names = array(
            'eng-US' => 'American type title',
            'eng-GB' => 'British type title'
        );
        $typeCreateStruct->descriptions = array(
            'eng-US' => 'American type description',
            'eng-GB' => 'British type description'
        );
        $typeCreateStruct->remoteId             = "test-remoteid-1";
        $typeCreateStruct->creatorId            = 23;
        $typeCreateStruct->creationDate         = new \DateTime();
        $typeCreateStruct->mainLanguageCode     = 'eng-GB';
        $typeCreateStruct->nameSchema           = "<name>";
        $typeCreateStruct->urlAliasSchema       = "<name>";

        $titleFieldCreate = $contentTypeService->newFieldDefinitionCreateStruct(
            'title',
            'ezstring'
        );
        $titleFieldCreate->names = array(
            'eng-US' => 'American title field name',
            'eng-GB' => 'British title field name',
        );
        $titleFieldCreate->descriptions = array(
            'eng-US' => 'American title field description',
            'eng-GB' => 'British title field description',
        );
        $titleFieldCreate->fieldGroup      = 'blog-content';
        $titleFieldCreate->position        = 1;
        $titleFieldCreate->isTranslatable  = true;
        $titleFieldCreate->isRequired      = true;
        $titleFieldCreate->isInfoCollector = false;
        $titleFieldCreate->isSearchable    = true;
        $titleFieldCreate->defaultValue    = new \eZ\Publish\SPI\Persistence\Content\FieldValue(
            array(
                "data" => new \ezp\Content\FieldType\TextLine\Value( 'New text line' )
            )
        );
        //$titleFieldCreate->validators
        //$titleFieldCreate->fieldSettings
        $typeCreateStruct->addFieldDefinition( $titleFieldCreate );

        $type1 = $contentTypeService->createContentType(
            $typeCreateStruct,
            array( $group )
        );
        $contentTypeService->publishContentTypeDraft( $type1 );

        $typeCreateStruct->identifier = "test-type-2";
        $typeCreateStruct->remoteId   = "test-remoteid-2";
        $type2 = $contentTypeService->createContentType(
            $typeCreateStruct,
            array( $group )
        );
        $contentTypeService->publishContentTypeDraft( $type2 );

        /* BEGIN: Use Case */
        $loadedTypes = $contentTypeService->loadContentTypes( $group );
        /* END: Use Case */

        $this->assertInternalType(
            'array',
            $loadedTypes
        );

        return $loadedTypes;
    }

    /**
     * Test for the loadContentTypeGroups() method.
     *
     * @depends testLoadContentTypes
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::loadContentTypes()
     *
     * @param array $types
     * @return void
     */
    public function testLoadContentTypesIdentifiers( array $types )
    {
        $expectedIdentifiers = array( 'test-type-1' => true, 'test-type-2' => true );

        $this->assertEquals( count( $expectedIdentifiers ), count( $types ) );

        $actualIdentifiers  = array( 'test-type-1' => false, 'test-type-2' => false );

        foreach ( $types as $type )
        {
            $actualIdentifiers[$type->identifier] = true;
        }

        $this->assertEquals(
            $expectedIdentifiers,
            $actualIdentifiers,
            'Identifier mismatch in loaded types.'
        );
    }

    /**
     * Test for the createContentTypeDraft() method.
     *
     * @depends testCreateContentType
     * @depends testPublishContentTypeDraft
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::createContentTypeDraft()
     *
     * @return array
     */
    public function testCreateContentTypeDraft()
    {
        $publishedType = $this->createPublishedContentType();

        /* BEGIN: Use case */
        $contentTypeService = $this->repository->getContentTypeService();

        $draftType = $contentTypeService->createContentTypeDraft( $publishedType );
        /* END: Use case */

        $this->assertInstanceOf(
            'eZ\\Publish\\API\\Repository\\Values\\ContentType\\ContentTypeDraft',
            $draftType
        );

        return array(
            "expected" => $publishedType,
            "actual"   => $draftType
        );
    }

    /**
     * Test for the createContentTypeDraft() method.
     *
     * @depends testCreateContentTypeDraft
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::createContentTypeDraft()
     *
     * @todo test for modifierId when users are plugged in
     *
     * @param array $data
     */
    public function testCreateContentTypeDraftValues( array $data )
    {
        /** @var $publishedType \eZ\Publish\Core\Repository\Values\ContentType\ContentType */
        $publishedType = $data['expected'];
        /** @var $draftType \eZ\Publish\Core\Repository\Values\ContentType\ContentTypeDraft */
        $draftType = $data['actual'];

        $skipProperties = array(
            "id",
            "status",
            "modificationDate",
            "modifierId"
        );
        $skipFieldProperties = array(
            "id"
        );
        $this->assertLoadContentTypeValues(
            $data,
            $skipProperties,
            $skipFieldProperties
        );

        $this->assertEquals(
            $publishedType->id,
            $draftType->id
        );

        $this->assertEquals(
            $draftType->status,
            ContentType::STATUS_DRAFT
        );
    }

    /**
     * Test for the createContentTypeDraft() method.
     *
     * @depends testCreateContentTypeDraft
     * @expectedException \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::createContentTypeDraft()
     *
     * @return void
     */
    public function testCreateContentTypeDraftThrowsUnauthorizedException()
    {
        $this->markTestIncomplete( "Test for ContentTypeService::createContentTypeDraft() is not implemented." );
    }

    /**
     * Test for the createContentTypeDraft() method.
     *
     * @depends testCreateContentTypeDraft
     * @expectedException \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::createContentTypeDraft()
     *
     * @return void
     */
    public function testCreateContentTypeDraftThrowsBadStateException()
    {
        // @todo needs users
        $this->markTestIncomplete( "Test for ContentTypeService::createContentTypeDraft() is not implemented." );
    }

    /**
     * Test for the updateContentTypeDraft() method.
     *
     * @depends testCreateContentType
     * @depends testLoadContentTypeDraft
     * @depends testNewContentTypeUpdateStruct
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::updateContentTypeDraft()
     *
     * @return array
     */
    public function testUpdateContentTypeDraft()
    {
        $contentTypeDraft = $this->createDraftContentType();

        /* BEGIN: Use Case */
        // $contentTypeDraft contains a ContentTypeDraft

        $contentTypeService = $this->repository->getContentTypeService();

        $typeUpdate = $contentTypeService->newContentTypeUpdateStruct();
        $typeUpdate->identifier             = 'news-article';
        $typeUpdate->remoteId               = '4cf35f5166fd31bf0cda859dc837e095daee9833';
        $typeUpdate->urlAliasSchema         = 'url@alias|scheme';
        $typeUpdate->nameSchema             = '@name@scheme@';
        $typeUpdate->isContainer            = true;
        $typeUpdate->mainLanguageCode       = 'eng-US';
        $typeUpdate->defaultAlwaysAvailable = false;
        $typeUpdate->modifierId             = 42;
        $typeUpdate->modificationDate       = new \DateTime();
        $typeUpdate->defaultSortField       = Location::SORT_FIELD_PUBLISHED;
        $typeUpdate->defaultSortOrder       = Location::SORT_ORDER_ASC;
        $typeUpdate->names                  = array(
            'eng-US' => 'News article',
            'eng-GB' => 'Nachrichten-Artikel',
        );
        $typeUpdate->descriptions = array(
            'eng-US' => 'A news article',
            'eng-GB'  => 'Ein Nachrichten-Artikel',
        );

        $contentTypeService->updateContentTypeDraft( $contentTypeDraft, $typeUpdate );
        /* END: Use Case */

        $updatedType = $contentTypeService->loadContentTypeDraft(
            $contentTypeDraft->id
        );

        $this->assertInstanceOf(
            'eZ\\Publish\\API\\Repository\\Values\\ContentType\\ContentTypeDraft',
            $updatedType
        );

        return array(
            'originalType' => $contentTypeDraft,
            'updateStruct' => $typeUpdate,
            'updatedType'  => $updatedType,
        );
    }

    /**
     * Test for the updateContentTypeDraft() method.
     *
     * @depends testUpdateContentTypeDraft
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::updateContentTypeDraft()
     *
     * @param array $data
     * @return void
     */
    public function testUpdateContentTypeDraftStructValues( array $data )
    {
        $originalType = $data['originalType'];
        $updateStruct = $data['updateStruct'];
        $updatedType  = $data['updatedType'];

        $this->assertPropertiesCorrect(
            array(
                 'id'                => $originalType->id,
                 'names'             => $updateStruct->names,
                 'descriptions'      => $updateStruct->descriptions,
                 'identifier'        => $updateStruct->identifier,
                 'creationDate'      => $originalType->creationDate,
                 'modificationDate'  => $updateStruct->modificationDate,
                 'creatorId'         => $originalType->creatorId,
                 'modifierId'        => $updateStruct->modifierId,
                 'urlAliasSchema'    => $updateStruct->urlAliasSchema,
                 'nameSchema'        => $updateStruct->nameSchema,
                 'isContainer'       => $updateStruct->isContainer,
                 'mainLanguageCode'  => $updateStruct->mainLanguageCode,
                 'contentTypeGroups' => $originalType->contentTypeGroups,
                 //'fieldDefinitions'  => $originalType->fieldDefinitions,
            ),
            $updatedType
        );

        $this->assertEquals(
            count( $originalType->fieldDefinitions ),
            count( $updatedType->fieldDefinitions ),
            "Field count in stored and loaded content type groups does not match"
        );

        foreach ( $originalType->fieldDefinitions as $index => $expectedFieldDefinition )
        {
            $actualFieldDefinition = $updatedType->fieldDefinitions[$index];
            $this->assertInstanceOf(
                'eZ\\Publish\\API\\Repository\\Values\\ContentType\\FieldDefinition',
                $actualFieldDefinition
            );
            $this->assertFieldDefinitionsEqual(
                $expectedFieldDefinition,
                $actualFieldDefinition
            );
        }
    }

    /**
     * Test for the updateContentTypeDraft() method.
     *
     * @depends testUpdateContentTypeDraft
     * @expectedException \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::updateContentTypeDraft()
     *
     * @return void
     */
    public function testUpdateContentTypeDraftThrowsUnauthorizedException()
    {
        $this->markTestIncomplete( "Test for ContentTypeService::updateContentTypeDraft() is not implemented." );
    }

    /**
     * Test for the updateContentTypeDraft() method.
     *
     * @depends testUpdateContentTypeDraft
     * @expectedException \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::updateContentTypeDraft()
     *
     * @return void
     */
    public function testUpdateContentTypeDraftThrowsInvalidArgumentExceptionDuplicateIdentifier()
    {
        $contentTypeDraft = $this->createDraftContentType();

        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        // $contentTypeDraft contains a ContentTypeDraft with identifier 'blog-post'

        $contentTypeService = $repository->getContentTypeService();

        $typeUpdate = $contentTypeService->newContentTypeUpdateStruct();
        $typeUpdate->identifier = 'folder';

        // Throws exception, since type "folder" already exists
        $contentTypeService->updateContentTypeDraft( $contentTypeDraft, $typeUpdate );
        /* END: Use Case */
    }

    /**
     * Test for the updateContentTypeDraft() method.
     *
     * @depends testUpdateContentTypeDraft
     * @expectedException \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::updateContentTypeDraft()
     *
     * @return void
     */
    public function testUpdateContentTypeDraftThrowsInvalidArgumentExceptionDuplicateRemoteId()
    {
        $contentTypeDraft = $this->createDraftContentType();

        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        // $contentTypeDraft contains a ContentTypeDraft with identifier 'blog-post'

        $contentTypeService = $repository->getContentTypeService();

        $typeUpdate = $contentTypeService->newContentTypeUpdateStruct();
        $typeUpdate->remoteId = 'a3d405b81be900468eb153d774f4f0d2';

        // Throws exception, since remote ID of type "folder" is used
        $contentTypeService->updateContentTypeDraft( $contentTypeDraft, $typeUpdate );
        /* END: Use Case */
    }

    /**
     * Test for the updateContentTypeDraft() method.
     *
     * @depends testUpdateContentTypeDraft
     * @expectedException \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::updateContentTypeDraft()
     *
     * @return void
     */
    public function testUpdateContentTypeDraftThrowsInvalidArgumentExceptionNoDraftForAuthenticatedUser()
    {
        // @todo: needs users
        $this->markTestIncomplete( "Test for ContentTypeService::updateContentType() is not implemented." );
    }

    /**
     * Test for the deleteContentType() method.
     *
     * @depends testLoadContentTypeByIdentifier
     * @depends testLoadContentType
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::deleteContentType()
     *
     * @return void
     */
    public function testDeleteContentType()
    {
        /* BEGIN: Use Case */
        $contentTypeService = $this->repository->getContentTypeService();

        $commentType = $contentTypeService->loadContentTypeByIdentifier( 'comment' );

        $contentTypeService->deleteContentType( $commentType );
        /* END: Use Case */

        try
        {
            $contentTypeService->loadContentType( $commentType->id );
            $this->fail( 'Content type could be loaded after delete.' );
        }
        catch ( Exceptions\NotFoundException $e )
        {
            // All fine
        }
    }

    /**
     * Test for the deleteContentType() method.
     *
     * @depends testDeleteContentType
     * @expectedException \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::deleteContentType()
     *
     * @return void
     */
    public function testDeleteContentTypeThrowsBadStateException()
    {
        // TODO: Needs existing content objects
        $this->markTestIncomplete( "Test for ContentTypeService::deleteContentType() is not implemented." );
    }

    /**
     * Test for the deleteContentType() method.
     *
     * @depends testDeleteContentType
     * @expectedException \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::deleteContentType()
     *
     * @return void
     */
    public function testDeleteContentTypeThrowsUnauthorizedException()
    {
        $this->markTestIncomplete( "Test for ContentTypeService::deleteContentType() is not implemented." );
    }

    /**
     * Test for the copyContentType() method.
     *
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::copyContentType()
     *
     * @return array
     * @todo needs users
     */
    public function testCopyContentType()
    {
        $this->markTestIncomplete( "Test for ContentTypeService::copyContentType() is not implemented." );
    }

    /**
     * Test for the copyContentType() method.
     *
     * @depends testCopyContentType
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::copyContentType()
     *
     * @return void
     */
    public function testCopyContentTypeValues()
    {
        $this->markTestIncomplete( "Test for ContentTypeService::copyContentType() is not implemented." );
    }

    /**
     * Test for the copyContentType() method.
     *
     * @depends testCopyContentType
     * @expectedException \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::copyContentType()
     *
     * @return void
     */
    public function testCopyContentTypeThrowsUnauthorizedException()
    {
        $this->markTestIncomplete( "Test for ContentTypeService::copyContentType() is not implemented." );
    }

    /**
     * Test for the assignContentTypeGroup() method.
     *
     * @depends testLoadContentTypeGroupByIdentifier
     * @depends testLoadContentTypeByIdentifier
     * @depends testLoadContentType
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::assignContentTypeGroup()
     *
     * @return void
     */
    public function testAssignContentTypeGroup()
    {
        /* BEGIN: Use Case */
        $contentTypeService = $this->repository->getContentTypeService();

        $mediaGroup = $contentTypeService->loadContentTypeGroupByIdentifier( 'Media' );
        $folderType = $contentTypeService->loadContentTypeByIdentifier( 'folder' );

        $contentTypeService->assignContentTypeGroup( $folderType, $mediaGroup );
        /* END: Use Case */

        $loadedType = $contentTypeService->loadContentType( $folderType->id );

        foreach ( $loadedType->contentTypeGroups as $loadedGroup )
        {
            if ( $mediaGroup->id == $loadedGroup->id )
            {
                return;
            }
        }
        $this->fail(
            sprintf(
                'Group with ID "%s" not assigned to content type.',
                $mediaGroup->id
            )
        );
    }

    /**
     * Test for the assignContentTypeGroup() method.
     *
     * @depends testAssignContentTypeGroup
     * @expectedException \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::assignContentTypeGroup()
     *
     * @return void
     */
    public function testAssignContentTypeGroupThrowsUnauthorizedException()
    {
        $this->markTestIncomplete( "Test for ContentTypeService::assignContentTypeGroup() is not implemented." );
    }

    /**
     * Test for the assignContentTypeGroup() method.
     *
     * @depends testAssignContentTypeGroup
     * @expectedException \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::assignContentTypeGroup()
     *
     * @return void
     */
    public function testAssignContentTypeGroupThrowsInvalidArgumentException()
    {
        /* BEGIN: Use Case */
        $contentTypeService = $this->repository->getContentTypeService();

        $folderType = $contentTypeService->loadContentTypeByIdentifier( 'folder' );
        $assignedGroups = $folderType->contentTypeGroups;

        foreach ( $assignedGroups as $assignedGroup )
        {
            // Throws an exception, since group is already assigned
            $contentTypeService->assignContentTypeGroup( $folderType, $assignedGroup );
        }
        /* END: Use Case */
    }

    /**
     * Test for the unassignContentTypeGroup() method.
     *
     * @depends testLoadContentTypeGroupByIdentifier
     * @depends testLoadContentTypeByIdentifier
     * @depends testLoadContentType
     * @depends testAssignContentTypeGroup
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::unassignContentTypeGroup()
     *
     * @return void
     */
    public function testUnassignContentTypeGroup()
    {
        /* BEGIN: Use Case */
        $contentTypeService = $this->repository->getContentTypeService();

        $folderType = $contentTypeService->loadContentTypeByIdentifier( 'folder' );

        $mediaGroup   = $contentTypeService->loadContentTypeGroupByIdentifier( 'Media' );
        $contentGroup = $contentTypeService->loadContentTypeGroupByIdentifier( 'Content' );

        // May not unassign last group
        $contentTypeService->assignContentTypeGroup( $folderType, $mediaGroup );

        $contentTypeService->unassignContentTypeGroup( $folderType, $contentGroup );
        /* END: Use Case */

        $loadedType = $contentTypeService->loadContentType( $folderType->id );

        foreach ( $loadedType->contentTypeGroups as $assignedGroup )
        {
            if ( $assignedGroup->id == $contentGroup->id )
            {
                $this->fail(
                    sprintf(
                        'Group with ID "%s" not unassigned.',
                        $contentGroup->id
                    )
                );
            }
        }
    }

    /**
     * Test for the unassignContentTypeGroup() method.
     *
     * @depends testUnassignContentTypeGroup
     * @expectedException \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::unassignContentTypeGroup()
     *
     * @return void
     */
    public function testUnassignContentTypeGroupThrowsUnauthorizedException()
    {
        $this->markTestIncomplete( "Test for ContentTypeService::unassignContentTypeGroup() is not implemented." );
    }

    /**
     * Test for the unassignContentTypeGroup() method.
     *
     * @depends testUnassignContentTypeGroup
     * @expectedException \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @covers \eZ\Publish\Covers\Repository\ContentTypeService::unassignContentTypeGroup()
     *
     * @return void
     */
    public function testUnassignContentTypeGroupThrowsInvalidArgumentException()
    {
        /* BEGIN: Use Case */
        $contentTypeService = $this->repository->getContentTypeService();

        $folderType = $contentTypeService->loadContentTypeByIdentifier( 'folder' );
        $notAssignedGroup = $contentTypeService->loadContentTypeGroupByIdentifier( 'Media' );

        // Throws an exception, since "Media" group is not assigned to "folder"
        $contentTypeService->unassignContentTypeGroup( $folderType, $notAssignedGroup );
        /* END: Use Case */
    }

    /**
     * Test for the unassignContentTypeGroup() method.
     *
     * @depends testUnassignContentTypeGroup
     * @expectedException \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::unassignContentTypeGroup()
     *
     * @return void
     */
    public function testUnassignContentTypeGroupThrowsBadStateException()
    {
        /* BEGIN: Use Case */
        $contentTypeService = $this->repository->getContentTypeService();

        $folderType     = $contentTypeService->loadContentTypeByIdentifier( 'folder' );
        $assignedGroups = $folderType->contentTypeGroups;

        foreach ( $assignedGroups as $assignedGroup )
        {
            // Throws an exception, when last group is to be removed
            $contentTypeService->unassignContentTypeGroup( $folderType, $assignedGroup );
        }
        /* END: Use Case */
    }

    /**
     * Test for the addFieldDefinition() method.
     *
     * @depends testCreateContentType
     * @depends testLoadContentTypeDraft
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::addFieldDefinition()
     *
     * @return array
     */
    public function testAddFieldDefinition()
    {
        $contentTypeDraft = $this->createDraftContentType();

        /* BEGIN: Use Case */
        // $contentTypeDraft contains a ContentTypeDraft

        $contentTypeService = $this->repository->getContentTypeService();

        $fieldDefCreate = $contentTypeService->newFieldDefinitionCreateStruct(
            'tags', 'ezstring'
        );
        $fieldDefCreate->names = array(
            'eng-US' => 'Tags',
            'de-DE' => 'Schlagworte',
        );
        $fieldDefCreate->descriptions = array(
            'eng-US' => 'Tags of the blog post',
            'de-DE' => 'Schlagworte des Blog-Eintrages',
        );
        $fieldDefCreate->fieldGroup      = 'blog-meta';
        $fieldDefCreate->position        = 1;
        $fieldDefCreate->isTranslatable  = true;
        $fieldDefCreate->isRequired      = true;
        $fieldDefCreate->isInfoCollector = false;
        $fieldDefCreate->defaultValue    = new \eZ\Publish\SPI\Persistence\Content\FieldValue(
            array(
                "data" => new \ezp\Content\FieldType\TextLine\Value( 'New text line' )
            )
        );
        //$fieldDefCreate->validators
        //$fieldDefCreate->fieldSettings
        $fieldDefCreate->isSearchable = true;

        $contentTypeService->addFieldDefinition( $contentTypeDraft, $fieldDefCreate );
        /* END: Use Case */

        $loadedType = $contentTypeService->loadContentTypeDraft( $contentTypeDraft->id );

        $this->assertInstanceOf(
            'eZ\\Publish\\API\\Repository\\Values\\ContentType\\ContentTypeDraft',
            $loadedType
        );
        return array(
            'loadedType'     => $loadedType,
            'fieldDefCreate' => $fieldDefCreate,
        );
    }

    /**
     * Test for the addFieldDefinition() method.
     *
     * @depends testAddFieldDefinition
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::addFieldDefinition()
     *
     * @param array $data
     * @return void
     */
    public function testAddFieldDefinitionStructValues( array $data )
    {
        $loadedType     = $data['loadedType'];
        $fieldDefCreate = $data['fieldDefCreate'];

        foreach ( $loadedType->fieldDefinitions as $fieldDefinition )
        {
            if ( $fieldDefinition->identifier == $fieldDefCreate->identifier )
            {
                $this->assertFieldDefinitionsCreatesEqual( $fieldDefCreate, $fieldDefinition );
                return;
            }
        }

        $this->fail(
            sprintf(
                'Field definition with identifier "%s" not created.',
                $fieldDefCreate->identifier
            )
        );
    }

    /**
     * Test for the addFieldDefinition() method.
     *
     * @depends testAddFieldDefinition
     * @expectedException \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::addFieldDefinition()
     *
     * @return void
     */
    public function testAddFieldDefinitionThrowsInvalidArgumentExceptionDuplicateFieldIdentifier()
    {
        $contentTypeDraft = $this->createDraftContentType();

        /* BEGIN: Use Case */
        // $contentTypeDraft contains a ContentTypeDraft
        // $contentTypeDraft has a field "title"

        $contentTypeService = $this->repository->getContentTypeService();

        $fieldDefCreate = $contentTypeService->newFieldDefinitionCreateStruct(
            'title', 'string'
        );

        // Throws an exception
        $contentTypeService->addFieldDefinition( $contentTypeDraft, $fieldDefCreate );
        /* END: Use Case */
    }

    /**
     * Test for the addFieldDefinition() method.
     *
     * @expectedException \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::addFieldDefinition()
     *
     * @return void
     */
    public function testAddFieldDefinitionThrowsUnauthorizedException()
    {
        $this->markTestIncomplete( "Test for ContentTypeService::addFieldDefinition() is not implemented." );
    }

    /**
     * Test for the removeFieldDefinition() method.
     *
     * @depends testCreateContentType
     * @depends testLoadContentTypeDraft
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::removeFieldDefinition()
     *
     * @return array
     */
    public function testRemoveFieldDefinition()
    {
        $contentTypeDraft = $this->createDraftContentType();
        $draftId = $contentTypeDraft->id;

        /* BEGIN: Use Case */
        // $draftId contains the ID of a content type draft
        $contentTypeService = $this->repository->getContentTypeService();

        $contentTypeDraft = $contentTypeService->loadContentTypeDraft( $draftId );

        $bodyField = $contentTypeDraft->getFieldDefinition( 'body' );

        $contentTypeService->removeFieldDefinition( $contentTypeDraft, $bodyField );
        /* END: Use Case */

        $loadedType = $contentTypeService->loadContentTypeDraft( $contentTypeDraft->id );

        $this->assertInstanceOf(
            'eZ\\Publish\\API\\Repository\\Values\\ContentType\\ContentTypeDraft',
            $loadedType
        );

        return array(
            'removedFieldDefinition' => $bodyField,
            'loadedType'             => $loadedType,
        );
    }

    /**
     * Test for the removeFieldDefinition() method.
     *
     * @depends testRemoveFieldDefinition
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::removeFieldDefinition()
     *
     * @param array $data
     * @return void
     */
    public function testRemoveFieldDefinitionRemoved( array $data )
    {
        $removedFieldDefinition = $data['removedFieldDefinition'];
        $loadedType = $data['loadedType'];

        foreach ( $loadedType->fieldDefinitions as $fieldDefinition )
        {
            if ( $fieldDefinition->identifier == $removedFieldDefinition->identifier )
            {
                $this->fail(
                    sprintf(
                        'Field definition with identifier "%s" not removed.',
                        $removedFieldDefinition->identifier
                    )
                );
            }
        }
    }

    /**
     * Test for the removeFieldDefinition() method.
     *
     * @depends testRemoveFieldDefinition
     * @expectedException \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::removeFieldDefinition()
     *
     * @return void
     */
    public function testRemoveFieldDefinitionThrowsInvalidArgumentException()
    {
        $contentTypeDraft = $this->createDraftContentType();
        $draftId = $contentTypeDraft->id;

        /* BEGIN: Use Case */
        // $draftId contains the ID of a content type draft
        $contentTypeService = $this->repository->getContentTypeService();

        $contentTypeDraft = $contentTypeService->loadContentTypeDraft( $draftId );

        $bodyField = $contentTypeDraft->getFieldDefinition( 'body' );
        $contentTypeService->removeFieldDefinition( $contentTypeDraft, $bodyField );

        $loadedDraft = $contentTypeService->loadContentTypeDraft( $draftId );

        // Throws exception, sine "body" has already been removed
        $contentTypeService->removeFieldDefinition( $loadedDraft, $bodyField );
        /* END: Use Case */
    }

    /**
     * Test for the removeFieldDefinition() method.
     *
     * @depends testRemoveFieldDefinition
     * @expectedException \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::removeFieldDefinition()
     *
     * @return void
     */
    public function testRemoveFieldDefinitionThrowsUnauthorizedException()
    {
        $this->markTestIncomplete( "Test for ContentTypeService::removeFieldDefinition() is not implemented." );
    }

    /**
     * Test for the updateFieldDefinition() method.
     *
     * @depends testCreateContentType
     * @depends testLoadContentTypeDraft
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::updateFieldDefinition()
     *
     * @return array
     */
    public function testUpdateFieldDefinition()
    {
        $contentTypeDraft = $this->createDraftContentType();
        $draftId = $contentTypeDraft->id;

        /* BEGIN: Use Case */
        // $draftId contains the ID of a content type draft
        $contentTypeService = $this->repository->getContentTypeService();

        $contentTypeDraft = $contentTypeService->loadContentTypeDraft( $draftId );

        $bodyField = $contentTypeDraft->getFieldDefinition( 'body' );

        $bodyUpdateStruct = $contentTypeService->newFieldDefinitionUpdateStruct();
        $bodyUpdateStruct->identifier = 'blog-body';
        $bodyUpdateStruct->names = array(
            'eng-US' => 'Blog post body',
            'de-DE' => 'Blog-Eintrags-Textkörper',
        );
        $bodyUpdateStruct->descriptions = array(
            'eng-US' => 'Blog post body of the blog post',
            'de-DE' => 'Blog-Eintrags-Textkörper des Blog-Eintrages',
        );
        $bodyUpdateStruct->fieldGroup      = 'updated-blog-content';
        $bodyUpdateStruct->position        = 3;
        $bodyUpdateStruct->isTranslatable  = false;
        $bodyUpdateStruct->isRequired      = false;
        $bodyUpdateStruct->isInfoCollector = true;
        $bodyUpdateStruct->defaultValue    = new \eZ\Publish\SPI\Persistence\Content\FieldValue(
            array(
                 "data" => new \ezp\Content\FieldType\TextBlock\Value( '' )
            )
        );
        //$bodyUpdateStruct->validators
        $bodyUpdateStruct->fieldSettings   = array(
            "textColumns" => 80
        );
        $bodyUpdateStruct->isSearchable = false;

        $contentTypeService->updateFieldDefinition(
            $contentTypeDraft,
            $bodyField,
            $bodyUpdateStruct
        );
        /* END: Use Case */

        $loadedDraft = $contentTypeService->loadContentTypeDraft( $draftId );
        $loadedField = $loadedDraft->getFieldDefinition( 'blog-body' );
        $this->assertInstanceOf(
            'eZ\\Publish\\API\\Repository\\Values\\ContentType\\FieldDefinition',
            $loadedField
        );

        return array(
            'originalField' => $bodyField,
            'updatedField'  => $loadedField,
            'updateStruct'  => $bodyUpdateStruct,
        );
    }

    /**
     * Test for the updateFieldDefinition() method.
     *
     * @depends testUpdateFieldDefinition
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::updateFieldDefinition()
     *
     * @param array $data
     * @return void
     */
    public function testUpdateFieldDefinitionStructValues( array $data )
    {
        $originalField = $data['originalField'];
        $updatedField  = $data['updatedField'];
        $updateStruct  = $data['updateStruct'];

        $this->assertPropertiesCorrect(
            array(
                'id'                  => $originalField->id,
                'identifier'          => $updateStruct->identifier,
                'names'               => $updateStruct->names,
                'descriptions'        => $updateStruct->descriptions,
                'fieldGroup'          => $updateStruct->fieldGroup,
                'position'            => $updateStruct->position,
                'fieldTypeIdentifier' => $originalField->fieldTypeIdentifier,
                'isTranslatable'      => $updateStruct->isTranslatable,
                'isRequired'          => $updateStruct->isRequired,
                'isInfoCollector'     => $updateStruct->isInfoCollector,
                'validators'          => $updateStruct->validators,
                'defaultValue'        => $originalField->defaultValue,
                'isSearchable'        => $updateStruct->isSearchable,
            ),
            $updatedField
        );
    }

    /**
     * Test for the updateFieldDefinition() method.
     *
     * @depends testUpdateFieldDefinition
     * @expectedException \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::updateFieldDefinition()
     *
     * @return void
     */
    public function testUpdateFieldDefinitionThrowsInvalidArgumentExceptionFieldIdentifierExists()
    {
        $contentTypeDraft = $this->createDraftContentType();
        $draftId = $contentTypeDraft->id;

        /* BEGIN: Use Case */
        // $draftId contains the ID of a content type draft
        $contentTypeService = $this->repository->getContentTypeService();

        $contentTypeDraft = $contentTypeService->loadContentTypeDraft( $draftId );

        $bodyField  = $contentTypeDraft->getFieldDefinition( 'body' );
        $titleField = $contentTypeDraft->getFieldDefinition( 'title' );

        $bodyUpdateStruct = $contentTypeService->newFieldDefinitionUpdateStruct();
        $bodyUpdateStruct->identifier = 'title';

        // Throws exception, since "title" field already exists
        $contentTypeService->updateFieldDefinition(
            $contentTypeDraft,
            $bodyField,
            $bodyUpdateStruct
        );
        /* END: Use Case */
    }

    /**
     * Test for the updateFieldDefinition() method.
     *
     * @depends testUpdateFieldDefinition
     * @expectedException \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::updateFieldDefinition()
     *
     * @return void
     */
    public function testUpdateFieldDefinitionThrowsUnauthorizedException()
    {
        $this->markTestIncomplete( "Test for ContentTypeService::updateFieldDefinition() is not implemented." );
    }

    /**
     * Test for the updateFieldDefinition() method.
     *
     * @depends testUpdateFieldDefinition
     * @expectedException \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @covers \eZ\Publish\Core\Repository\ContentTypeService::updateFieldDefinition()
     *
     * @return void
     */
    public function testUpdateFieldDefinitionThrowsInvalidArgumentExceptionFieldIdNotFound()
    {
        $contentTypeDraft = $this->createDraftContentType();
        $draftId = $contentTypeDraft->id;

        /* BEGIN: Use Case */
        // $draftId contains the ID of a content type draft
        $contentTypeService = $this->repository->getContentTypeService();

        $contentTypeDraft = $contentTypeService->loadContentTypeDraft( $draftId );

        $bodyField = $contentTypeDraft->getFieldDefinition( 'body' );
        $contentTypeService->removeFieldDefinition( $contentTypeDraft, $bodyField );

        $loadedDraft = $contentTypeService->loadContentTypeDraft( $draftId );

        $bodyUpdateStruct = $contentTypeService->newFieldDefinitionUpdateStruct();

        // Throws exception, since field "body" is already deleted
        $contentTypeService->updateFieldDefinition(
            $loadedDraft,
            $bodyField,
            $bodyUpdateStruct
        );
        /* END: Use Case */
    }
}