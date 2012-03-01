<?php
/**
 * File containing the ContentTypeServiceTest class
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\API\Repository\Tests;

use \eZ\Publish\API\Repository\Tests\BaseTest;
use eZ\Publish\API\Repository\Values\Content\Location;
use \eZ\Publish\API\Repository\Values\ContentType\ContentType;
use \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup;
use \eZ\Publish\API\Repository\Exceptions;

use \eZ\Publish\API\Repository\Tests\Stubs\Values\ContentType\StringLengthValidatorStub;

/**
 * Test case for operations in the ContentTypeService using in memory storage.
 *
 * @see eZ\Publish\API\Repository\ContentTypeService
 */
class ContentTypeServiceTest extends BaseTest
{
    /**
     * Test for the newContentTypeGroupCreateStruct() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::newContentTypeGroupCreateStruct()
     * @dep_ends eZ\Publish\API\Repository\Tests\RepositoryTest::testGetContentTypeService
     */
    public function testNewContentTypeGroupCreateStruct()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $groupCreate = $contentTypeService->newContentTypeGroupCreateStruct(
            'new-group'
        );
        /* END: Use Case */

        $this->assertInstanceOf(
            '\\eZ\\Publish\\API\\Repository\\Values\\ContentType\\ContentTypeGroupCreateStruct',
            $groupCreate
        );
        return $groupCreate;
    }

    /**
     * Test for the newContentTypeGroupCreateStruct() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::newContentTypeGroupCreateStruct()
     * @depends eZ\Publish\API\Repository\Tests\ContentTypeServiceTest::testNewContentTypeGroupCreateStruct
     */
    public function testNewContentTypeGroupCreateStructValues( $createStruct )
    {
        $this->assertPropertiesCorrect(
            array(
                'identifier'       => 'new-group',
                'creatorId'        => null,
                'creationDate'     => null,
                'mainLanguageCode' => null,
            ),
            $createStruct
        );
    }

    /**
     * Test for the createContentTypeGroup() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::createContentTypeGroup()
     * @dep_ends eZ\Publish\API\Repository\Tests\ContentTypeServiceTest::testNewContentTypeGroupCreateStruct
     */
    public function testCreateContentTypeGroup()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $groupCreate = $contentTypeService->newContentTypeGroupCreateStruct(
            'new-group'
        );
        $groupCreate->creatorId        = 23;
        $groupCreate->creationDate     = new \DateTime();
        $groupCreate->mainLanguageCode = 'de-DE';
        $groupCreate->names            = array( 'eng-US' => 'A name.' );
        $groupCreate->descriptions     = array( 'eng-US' => 'A description.' );

        $group = $contentTypeService->createContentTypeGroup( $groupCreate );
        /* END: Use Case */

        $this->assertInstanceOf(
            '\\eZ\\Publish\\API\\Repository\\Values\\ContentType\\ContentTypeGroup',
            $group
        );

        return array(
            'createStruct' => $groupCreate,
            'group'        => $group,
        );
    }

    /**
     * Test for the createContentTypeGroup() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::createContentTypeGroup()
     * @depends eZ\Publish\API\Repository\Tests\ContentTypeServiceTest::testCreateContentTypeGroup
     */
    public function testCreateContentTypeGroupStructValues( array $data )
    {
        $createStruct = $data['createStruct'];
        $group        = $data['group'];

        $this->assertStructPropertiesCorrect(
            $createStruct,
            $group,
            array( 'names', 'descriptions' )
        );
        $this->assertNotNull(
            $group->id
        );
    }

    /**
     * Test for the createContentTypeGroup() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::createContentTypeGroup()
     * @expectedException \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @dep_ends eZ\Publish\API\Repository\Tests\ContentTypeServiceTest::testCreateContentTypeGroup
     */
    public function testCreateContentTypeGroupThrowsInvalidArgumentException()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $groupCreate = $contentTypeService->newContentTypeGroupCreateStruct(
            'Content'
        );

        // Throws an Exception, since group "Content" already exists
        $group = $contentTypeService->createContentTypeGroup( $groupCreate );
        /* END: Use Case */
    }

    /**
     * Test for the loadContentTypeGroup() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::loadContentTypeGroup()
     * @dep_ends eZ\Publish\API\Repository\Tests\ContentTypeServiceTest::testCreateContentTypeGroup
     */
    public function testLoadContentTypeGroup()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        // Loads the "Users" group
        $loadedGroup = $contentTypeService->loadContentTypeGroup( 2 );
        /* END: Use Case */

        $this->assertInstanceOf(
            '\\eZ\\Publish\\API\\Repository\\Values\\ContentType\\ContentTypeGroup',
            $loadedGroup
        );

        return $loadedGroup;
    }

    /**
     * Test for the loadContentTypeGroup() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::loadContentTypeGroup()
     * @depends eZ\Publish\API\Repository\Tests\ContentTypeServiceTest::testLoadContentTypeGroup
     */
    public function testLoadContentTypeGroupStructValues( ContentTypeGroup $group )
    {
        $this->assertPropertiesCorrect(
            array(
                'id'               =>  2,
                'identifier'       =>  'Users',
                'creationDate'     =>  new \DateTime( '@1031216941' ),
                'modificationDate' =>  new \DateTime( '@1033922113' ),
                'creatorId'        =>  14,
                'modifierId'       =>  14,
            ),
            $group
        );
    }

    /**
     * Test for the loadContentTypeGroup() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::loadContentTypeGroup()
     * @expectedException \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @dep_ends eZ\Publish\API\Repository\Tests\RepositoryTest::testGetContentTypeService
     */
    public function testLoadContentTypeGroupThrowsNotFoundException()
    {
        $repository = $this->getRepository();

        $contentTypeService = $repository->getContentTypeService();
        $loadedGroup = $contentTypeService->loadContentTypeGroup( 2342 );
    }

    /**
     * Test for the loadContentTypeGroupByIdentifier() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::loadContentTypeGroupByIdentifier()
     * @dep_ends eZ\Publish\API\Repository\Tests\RepositoryTest::testGetContentTypeService
     */
    public function testLoadContentTypeGroupByIdentifier()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $loadedGroup = $contentTypeService->loadContentTypeGroupByIdentifier(
            "Media"
        );
        /* END: Use Case */

        $this->assertInstanceOf(
            '\\eZ\\Publish\\API\\Repository\\Values\\ContentType\\ContentTypeGroup',
            $loadedGroup
        );

        return $loadedGroup;
    }

    /**
     * Test for the loadContentTypeGroupByIdentifier() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::loadContentTypeGroupByIdentifier()
     * @depends eZ\Publish\API\Repository\Tests\ContentTypeServiceTest::testLoadContentTypeGroupByIdentifier
     */
    public function testLoadContentTypeGroupByIdentifierStructValues( ContentTypeGroup $group )
    {
        $repository         = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        $this->assertEquals(
            $contentTypeService->loadContentTypeGroup( 3 ),
            $group
        );
    }

    /**
     * Test for the loadContentTypeGroupByIdentifier() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::loadContentTypeGroupByIdentifier()
     * @expectedException \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function testLoadContentTypeGroupByIdentifierThrowsNotFoundException()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        // Throws exception
        $loadedGroup = $contentTypeService->loadContentTypeGroupByIdentifier(
            'not-exists'
        );
        /* END: Use Case */
    }

    /**
     * Test for the loadContentTypeGroups() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::loadContentTypeGroups()
     * @dep_ends eZ\Publish\API\Repository\Tests\ContentTypeServiceTest::testCreateContentTypeGroup
     */
    public function testLoadContentTypeGroups()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        // Loads an array with all content type groups
        $loadedGroups = $contentTypeService->loadContentTypeGroups();
        /* END: Use Case */

        $this->assertInternalType(
            'array',
            $loadedGroups
        );

        return $loadedGroups;
    }

    /**
     * Test for the loadContentTypeGroups() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::loadContentTypeGroups()
     * @depends eZ\Publish\API\Repository\Tests\ContentTypeServiceTest::testLoadContentTypeGroups
     */
    public function testLoadContentTypeGroupsIdentifiers( $groups )
    {
        $this->assertEquals( 4, count( $groups ) );

        $expectedIdentifiers = array(
            'Content' => true,
            'Users'   => true,
            'Media'   => true,
            'Setup'   => true,
        );

        $actualIdentifiers = array();
        foreach ( $groups as $group )
        {
            $actualIdentifiers[$group->identifier] = true;
        }

        ksort( $expectedIdentifiers );
        ksort( $actualIdentifiers );

        $this->assertEquals(
            $expectedIdentifiers,
            $actualIdentifiers,
            'Identifier missmatch in loeaded groups.'
        );
    }

    /**
     * Test for the newContentTypeGroupUpdateStruct() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::newContentTypeGroupUpdateStruct()
     * @dep_ends eZ\Publish\API\Repository\Tests\RepositoryTest::testGetContentTypeService
     */
    public function testNewContentTypeGroupUpdateStruct()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $groupUpdate = $contentTypeService->newContentTypeGroupUpdateStruct();
        /* END: Use Case */

        $this->assertInstanceOf(
            '\\eZ\\Publish\\API\\Repository\\Values\\ContentType\\ContentTypeGroupUpdateStruct',
            $groupUpdate
        );
    }

    /**
     * Test for the updateContentTypeGroup() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::updateContentTypeGroup()
     * @dep_ends eZ\Publish\API\Repository\Tests\ContentTypeServiceTest::testNewContentTypeGroupUpdateStruct
     */
    public function testUpdateContentTypeGroup()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $group = $contentTypeService->loadContentTypeGroupByIdentifier( 'Setup' );

        $groupUpdate = $contentTypeService->newContentTypeGroupUpdateStruct();

        $groupUpdate->identifier       = 'Teardown';
        $groupUpdate->modifierId       = 42;
        $groupUpdate->modificationDate = new \DateTime();
        $groupUpdate->mainLanguageCode = 'eng-US';

        $groupUpdate->names = array(
            'eng-US' => 'A name',
            'eng-GB' => 'A name',
        );
        $groupUpdate->descriptions = array(
            'eng-US' => 'A description',
            'eng-GB' => 'A description',
        );

        $contentTypeService->updateContentTypeGroup( $group, $groupUpdate );
        /* END: Use Case */

        $updatedGroup = $contentTypeService->loadContentTypeGroup( $group->id );

        $this->assertInstanceOf(
            '\\eZ\\Publish\\API\\Repository\\Values\\ContentType\\ContentTypeGroupUpdateStruct',
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
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::updateContentTypeGroup()
     * @depends eZ\Publish\API\Repository\Tests\ContentTypeServiceTest::testUpdateContentTypeGroup
     */
    public function testUpdateContentTypeGroupStructValues( array $data )
    {
        $expectedValues = array(
            'identifier'       => $data['updateStruct']->identifier,
            'creationDate'     => $data['originalGroup']->creationDate,
            'modificationDate' => $data['updateStruct']->modificationDate,
            'creatorId'        => $data['originalGroup']->creatorId,
            'modifierId'       => $data['updateStruct']->modifierId,
            'mainLanguageCode' => $data['updateStruct']->mainLanguageCode,
            'names'            => $data['updateStruct']->names,
            'descriptions'     => $data['updateStruct']->descriptions,
        );

        $this->assertPropertiesCorrect(
            $expectedValues, $data['updatedGroup']
        );
    }

    /**
     * Test for the updateContentTypeGroup() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::updateContentTypeGroup()
     * @expectedException \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @dep_ends eZ\Publish\API\Repository\Tests\ContentTypeServiceTest::testUpdateContentTypeGroup
     */
    public function testUpdateContentTypeGroupThrowsInvalidArgumentException()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $group = $contentTypeService->loadContentTypeGroupByIdentifier(
            'Media'
        );

        $groupUpdate = $contentTypeService->newContentTypeGroupUpdateStruct();
        $groupUpdate->identifier = 'Users';

        // Exception, because group with identifier "Users" exists
        $contentTypeService->updateContentTypeGroup( $group, $groupUpdate );
        /* END: Use Case */
    }

    /**
     * Test for the deleteContentTypeGroup() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::deleteContentTypeGroup()
     * @dep_ends eZ\Publish\API\Repository\Tests\ContentTypeServiceTest::testCreateContentTypeGroup
     */
    public function testDeleteContentTypeGroup()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $groupCreate = $contentTypeService->newContentTypeGroupCreateStruct(
            'new-group'
        );
        $group = $contentTypeService->createContentTypeGroup( $groupCreate );

        // ...

        $group = $contentTypeService->loadContentTypeGroupByIdentifier( 'new-group' );

        $contentTypeService->deleteContentTypeGroup( $group );
        /* END: Use Case */

        try
        {
            $contentTypeService->loadContentTypeGroup( $group->id );
            $this->fail( 'Content type group not deleted.' );
        }
        catch ( \eZ\Publish\API\Repository\Exceptions\NotFoundException $e )
        {
            // All fine
        }
    }

    /**
     * Test for the newContentTypeCreateStruct() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::newContentTypeCreateStruct()
     * @dep_ends eZ\Publish\API\Repository\Tests\RepositoryTest::testGetContentTypeService
     */
    public function testNewContentTypeCreateStruct()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $typeCreate = $contentTypeService->newContentTypeCreateStruct(
            'new-type'
        );
        /* END: Use Case */

        $this->assertInstanceOf(
            '\\eZ\\Publish\\API\\Repository\\Values\\ContentType\\ContentTypeCreateStruct',
            $typeCreate
        );
        return $typeCreate;
    }

    /**
     * Test for the newContentTypeCreateStruct() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::newContentTypeCreateStruct()
     * @depends eZ\Publish\API\Repository\Tests\ContentTypeServiceTest::testNewContentTypeCreateStruct
     */
    public function testNewContentTypeCreateStructValues( $createStruct )
    {
        $this->assertPropertiesCorrect(
            array(
                'identifier'             => 'new-type',
                'mainLanguageCode'       => null,
                'remoteId'               => null,
                'urlAliasSchema'         => null,
                'nameSchema'             => null,
                'isContainer'            => false,
                'defaultSortField'       => Location::SORT_FIELD_PUBLISHED,
                'defaultSortOrder'       => Location::SORT_ORDER_DESC,
                'defaultAlwaysAvailable' => true,
                'names'                  => null,
                'descriptions'           => null,
                'creatorId'              => null,
                'creationDate'           => null,
            ),
            $createStruct
        );
    }

    /**
     * Test for the newFieldDefinitionCreateStruct() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::newFieldDefinitionCreateStruct()
     * @dep_ends eZ\Publish\API\Repository\Tests\RepositoryTest::testGetContentTypeService
     */
    public function testNewFieldDefinitionCreateStruct()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $fieldDefinitionCreate = $contentTypeService->newFieldDefinitionCreateStruct(
            'title', 'string'
        );
        /* END: Use Case */

        $this->assertInstanceOf(
            '\\eZ\\Publish\\API\\Repository\\Values\\ContentType\\FieldDefinitionCreateStruct',
            $fieldDefinitionCreate
        );
        return $fieldDefinitionCreate;
    }

    /**
     * Test for the newFieldDefinitionCreateStruct() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\FieldDefinitionService::newFieldDefinitionCreateStruct()
     * @depends eZ\Publish\API\Repository\Tests\ContentTypeServiceTest::testNewFieldDefinitionCreateStruct
     */
    public function testNewFieldDefinitionCreateStructValues( $createStruct )
    {
        $this->assertPropertiesCorrect(
            array(
                'fieldTypeIdentifier' => 'string',
                'identifier'          => 'title',
                'names'               => null,
                'descriptions'        => null,
                'fieldGroup'          => null,
                'position'            => null,
                'isTranslatable'      => null,
                'isRequired'          => null,
                'isInfoCollector'     => null,
                'validators'          => null,
                'fieldSettings'       => null,
                'defaultValue'        => null,
                'isSearchable'        => null,
            ),
            $createStruct
        );
    }

    /**
     * Test for the deleteContentTypeGroup() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::deleteContentTypeGroup()
     * @expectedException \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function testDeleteContentTypeGroupThrowsInvalidArgumentException()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $contentGroup = $contentTypeService->loadContentTypeGroupByIdentifier( 'Content' );

        // Throws exception, since group contains types
        $contentTypeService->deleteContentTypeGroup( $contentGroup );
        /* END: Use Case */
    }

    /**
     * Test for the createContentType() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::createContentType()
     * 
     */
    public function testCreateContentType()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $typeCreate = $contentTypeService->newContentTypeCreateStruct( 'blog-post' );
        $typeCreate->mainLanguageCode = 'eng-US';
        $typeCreate->remoteId         = '384b94a1bd6bc06826410e284dd9684887bf56fc';
        $typeCreate->urlAliasSchema   = 'url|scheme';
        $typeCreate->nameSchema       = 'name|scheme';
        $typeCreate->names            = array(
            'eng-US' => 'Blog post',
            'de-DE'  => 'Blog-Eintrag',
        );
        $typeCreate->descriptions = array(
            'eng-US' => 'A blog post',
            'de-DE'  => 'Ein Blog-Eintrag',
        );
        $typeCreate->creatorId = 23;
        $typeCreate->creationDate = new \DateTime();

        $titleFieldCreate = $contentTypeService->newFieldDefinitionCreateStruct(
            'title', 'string'
        );
        $titleFieldCreate->names = array(
            'eng-US' => 'Title',
            'de-DE'  => 'Titel',
        );
        $titleFieldCreate->descriptions = array(
            'eng-US' => 'Title of the blog post',
            'de-DE'  => 'Titel des Blog-Eintrages',
        );
        $titleFieldCreate->fieldGroup      = 'blog-content';
        $titleFieldCreate->position        = 1;
        $titleFieldCreate->isTranslatable  = true;
        $titleFieldCreate->isRequired      = true;
        $titleFieldCreate->isInfoCollector = false;
        $titleFieldCreate->validators      = array(
            new StringLengthValidatorStub(),
        );
        $titleFieldCreate->fieldSettings = array(
            'textblockheight' => 10
        );
        $titleFieldCreate->isSearchable = true;

        $typeCreate->addFieldDefinition( $titleFieldCreate );

        $bodyFieldCreate = $contentTypeService->newFieldDefinitionCreateStruct(
            'body', 'text'
        );
        $bodyFieldCreate->names = array(
            'eng-US' => 'Body',
            'de-DE'  => 'Textkörper',
        );
        $bodyFieldCreate->descriptions = array(
            'eng-US' => 'Body of the blog post',
            'de-DE'  => 'Textkörper des Blog-Eintrages',
        );
        $bodyFieldCreate->fieldGroup      = 'blog-content';
        $bodyFieldCreate->position        = 2;
        $bodyFieldCreate->isTranslatable  = true;
        $bodyFieldCreate->isRequired      = true;
        $bodyFieldCreate->isInfoCollector = false;
        $bodyFieldCreate->validators      = array(
            new StringLengthValidatorStub(),
        );
        $bodyFieldCreate->fieldSettings = array(
            'textblockheight' => 80
        );
        $bodyFieldCreate->isSearchable = true;

        $typeCreate->addFieldDefinition( $bodyFieldCreate );

        $groups = array(
            $contentTypeService->loadContentTypeGroupByIdentifier( 'Media' ),
            $contentTypeService->loadContentTypeGroupByIdentifier( 'Setup' )
        );

        $contentType = $contentTypeService->createContentType(
            $typeCreate,
            $groups
        );
        /* END: Use Case */

        $this->assertInstanceOf(
            'eZ\\Publish\\API\\Repository\\Values\\ContentType\\ContentType',
            $contentType
        );

        return array(
            'typeCreate'  => $typeCreate,
            'contentType' => $contentType,
            'groups'      => $groups,
        );
    }

    /**
     * Test for the createContentType() method struct values.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::createContentType()
     * @depends eZ\Publish\API\Repository\Tests\ContentTypeServiceTest::testCreateContentType
     */
    public function testCreateContentTypeStructValues( array $data )
    {
        $typeCreate  = $data['typeCreate'];
        $contentType = $data['contentType'];
        $groups      = $data['groups'];

        foreach ( $typeCreate as $propertyName => $propertyValue )
        {
            switch ( $propertyName )
            {
                case 'fieldDefinitions':
                    $this->assertFieldDefinitionsCorrect(
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
    }

    /**
     * Asserts field definition creation
     *
     * Asserts that all field definitions defined through created structs in
     * $expectedDefinitionCreates have been correctly created in
     * $actualDefinitions.
     *
     * @param \eZ\Publish\API\Repository\Values\FieldDefinitionCreateStruct[] $expectedDefinitionCreates
     * @param \eZ\Publish\API\Repository\Values\FieldDefinition[] $actualDefinitions
     * @return void
     */
    protected function assertFieldDefinitionsCorrect( array $expectedDefinitionCreates, array $actualDefinitions )
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
            $this->assertFieldDefinitionsEqual(
                $expectedCreate,
                $actualDefinitions[$key]
            );
        }
    }

    /**
     * Asserts that a field definition has been correctly created.
     *
     * Asserts that the given $actualDefinition is correctly created from the
     * create struct in $expectedCreate.
     *
     * @param \eZ\Publish\API\Repository\Values\FieldDefinitionCreateStruct $expectedDefinitionCreate
     * @param \eZ\Publish\API\Repository\Values\FieldDefinition $actualDefinition
     * @return void
     */
    protected function assertFieldDefinitionsEqual( $expectedCreate, $actualDefinition )
    {
        foreach ( $expectedCreate as $propertyName => $propertyValue )
        {
            $this->assertEquals(
                $expectedCreate->$propertyName,
                $actualDefinition->$propertyName
            );
        }
    }

    /**
     * Asserts that two sets of ContentTypeGroups are equal.
     *
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup[] $expectedGroups
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup[] $actualGroups
     * @return void
     */
    protected function assertContentTypeGroupsCorrect( $expectedGroups, $actualGroups )
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

        foreach ( $expectedGroups as $key => $expectedGroup )
        {
            $this->assertPropertiesCorrect(
                $expectedGroup,
                $actualGroups[$key],
                $this->groupProperties
            );
        }
    }

    /**
     * Test for the createContentType() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::createContentType()
     * @expectedException \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @depends eZ\Publish\API\Repository\Tests\ContentTypeServiceTest::testCreateContentType
     */
    public function testCreateContentTypeThrowsInvalidArgumentExceptionDuplicateIdentifier()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $typeCreate = $contentTypeService->newContentTypeCreateStruct( 'folder' );

        // Throws exception, since type "folder" exists
        $secondType = $contentTypeService->createContentType( $typeCreate, array() );
        /* END: Use Case */
    }

    /**
     * Test for the createContentType() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::createContentType()
     * @expectedException \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @depends eZ\Publish\API\Repository\Tests\ContentTypeServiceTest::testCreateContentType
     */
    public function testCreateContentTypeThrowsInvalidArgumentExceptionDuplicateRemoteId()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $typeCreate = $contentTypeService->newContentTypeCreateStruct( 'news-article' );
        $typeCreate->remoteId = 'a3d405b81be900468eb153d774f4f0d2';

        // Throws exception, since "folder" type has this remote ID
        $secondType = $contentTypeService->createContentType( $typeCreate, array() );
        /* END: Use Case */
    }

    /**
     * Test for the createContentType() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::createContentType()
     * @expectedException \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @depends eZ\Publish\API\Repository\Tests\ContentTypeServiceTest::testCreateContentType
     */
    public function testCreateContentTypeThrowsInvalidArgumentExceptionDuplicateFieldIdentifier()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $typeCreate = $contentTypeService->newContentTypeCreateStruct( 'blog-post' );

        $firstFieldCreate = $contentTypeService->newFieldDefinitionCreateStruct(
            'title', 'string'
        );
        $typeCreate->addFieldDefinition( $firstFieldCreate );

        $secondFieldCreate = $contentTypeService->newFieldDefinitionCreateStruct(
            'title', 'text'
        );
        $typeCreate->addFieldDefinition( $secondFieldCreate );

        // Throws exception, due to duplicate "title" field
        $secondType = $contentTypeService->createContentType( $typeCreate, array() );
        /* END: Use Case */
    }

    /**
     * Test for the newContentTypeUpdateStruct() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::newContentTypeUpdateStruct()
     * 
     */
    public function testNewContentTypeUpdateStruct()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $typeUpdate = $contentTypeService->newContentTypeUpdateStruct();
        /* END: Use Case */

        $this->assertInstanceOf(
            'eZ\\Publish\\API\\Repository\\Values\\ContentType\\ContentTypeUpdateStruct',
            $typeUpdate
        );
        return $typeUpdate;
    }

    /**
     * Test for the newContentTypeUpdateStruct() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::newContentTypeUpdateStruct()
     * @depends eZ\Publish\API\Repository\Tests\ContentTypeServiceTest::testNewContentTypeUpdateStruct
     */
    public function testNewContentTypeUpdateStructValues( $typeUpdate )
    {
        foreach ( $typeUpdate as $propertyName => $propertyValue )
        {
            $this->assertNull(
                $propertyValue,
                "Property '$propertyName' is not null."
            );
        }
    }

    /**
     * Test for the loadContentTypeDraft() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::loadContentTypeDraft()
     * 
     */
    public function testLoadContentTypeDraft()
    {
        $createdDraft = $this->createContentTypeDraft();
        $draftId = $createdDraft->id;

        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        // $draftId contains the ID of the draft to load
        $contentTypeService = $repository->getContentTypeService();

        $contentTypeDraft = $contentTypeService->loadContentTypeDraft( $draftId );
        /* END: Use Case */

        $this->assertEquals(
            $createdDraft,
            $contentTypeDraft
        );
    }

    /**
     * Test for the loadContentTypeDraft() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::loadContentTypeDraft()
     * @expectedException \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function testLoadContentTypeDraftThrowsNotFoundException()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        // Throws exception, since 2342 does not exist
        $contentTypeDraft = $contentTypeService->loadContentTypeDraft( 2342 );
        /* END: Use Case */
    }

    /**
     * Creates a fully functional ContentTypeDraft and returns it.
     *
     * @return \eZ\Publish\API\Repository\Values\ContentType\ContentTypeDraft
     */
    protected function createContentTypeDraft()
    {
        $repository         = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        $groups = array(
            $contentTypeService->loadContentTypeGroupByIdentifier( 'Content' ),
            $contentTypeService->loadContentTypeGroupByIdentifier( 'Setup' )
        );

        $typeCreate = $contentTypeService->newContentTypeCreateStruct( 'blog-post' );
        $typeCreate->mainLanguageCode = 'eng-US';
        $typeCreate->remoteId         = '384b94a1bd6bc06826410e284dd9684887bf56fc';
        $typeCreate->urlAliasSchema   = 'url|scheme';
        $typeCreate->nameSchema       = 'name|scheme';
        $typeCreate->names = array(
            'eng-US' => 'Blog post',
            'de-DE'  => 'Blog-Eintrag',
        );
        $typeCreate->descriptions = array(
            'eng-US' => 'A blog post',
            'de-DE'  => 'Ein Blog-Eintrag',
        );
        $typeCreate->creatorId    = 23;
        $typeCreate->creationDate = new \DateTime();

        $titleFieldCreate = $contentTypeService->newFieldDefinitionCreateStruct(
            'title', 'string'
        );
        $titleFieldCreate->names = array(
            'eng-US' => 'Title',
            'de-DE'  => 'Titel',
        );
        $titleFieldCreate->descriptions = array(
            'eng-US' => 'Title of the blog post',
            'de-DE'  => 'Titel des Blog-Eintrages',
        );
        $titleFieldCreate->fieldGroup      = 'blog-content';
        $titleFieldCreate->position        = 1;
        $titleFieldCreate->isTranslatable  = true;
        $titleFieldCreate->isRequired      = true;
        $titleFieldCreate->isInfoCollector = false;
        $titleFieldCreate->validators      = array(
            new StringLengthValidatorStub(),
        );
        $titleFieldCreate->fieldSettings = array(
            'textblockheight' => 10
        );
        $titleFieldCreate->isSearchable = true;

        $typeCreate->addFieldDefinition( $titleFieldCreate );

        $bodyFieldCreate = $contentTypeService->newFieldDefinitionCreateStruct(
            'body', 'text'
        );
        $bodyFieldCreate->names = array(
            'eng-US' => 'Body',
            'de-DE'  => 'Textkörper',
        );
        $bodyFieldCreate->descriptions = array(
            'eng-US' => 'Body of the blog post',
            'de-DE'  => 'Textkörper des Blog-Eintrages',
        );
        $bodyFieldCreate->fieldGroup      = 'blog-content';
        $bodyFieldCreate->position        = 2;
        $bodyFieldCreate->isTranslatable  = true;
        $bodyFieldCreate->isRequired      = true;
        $bodyFieldCreate->isInfoCollector = false;
        $bodyFieldCreate->validators      = array(
            new StringLengthValidatorStub(),
        );
        $bodyFieldCreate->fieldSettings = array(
            'textblockheight' => 80
        );
        $bodyFieldCreate->isSearchable = true;

        $typeCreate->addFieldDefinition( $bodyFieldCreate );

        return $contentTypeService->createContentType(
            $typeCreate,
            $groups
        );
    }

    /**
     * Test for the updateContentTypeDraft() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::updateContentTypeDraft()
     * 
     */
    public function testUpdateContentTypeDraft()
    {
        $contentTypeDraft = $this->createContentTypeDraft();

        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        // $contentTypeDraft contains a ContentTypeDraft

        $contentTypeService = $repository->getContentTypeService();

        $typeUpdate = $contentTypeService->newContentTypeUpdateStruct();
        $typeUpdate->identifier             = 'news-article';
        $typeUpdate->remoteId               = '4cf35f5166fd31bf0cda859dc837e095daee9833';
        $typeUpdate->urlAliasSchema         = 'url@alias|scheme';
        $typeUpdate->nameSchema             = '@name@scheme@';
        $typeUpdate->isContainer            = true;
        $typeUpdate->mainLanguageCode       = 'de-DE';
        $typeUpdate->defaultAlwaysAvailable = false;
        $typeUpdate->modifierId             = 42;
        $typeUpdate->modificationDate       = new \DateTime();
        $typeUpdate->names                  = array(
            'eng-US' => 'News article',
            'de-DE'  => 'Nachrichten-Artikel',
        );
        $typeUpdate->descriptions = array(
            'eng-US' => 'A news article',
            'de-DE'  => 'Ein Nachrichten-Artikel',
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
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::updateContentTypeDraft()
     * @depends eZ\Publish\API\Repository\Tests\ContentTypeServiceTest::testUpdateContentTypeDraft
     */
    public function testUpdateContentTypeDraftStructValues( $data )
    {
        $originalType = $data['originalType'];
        $updateStruct = $data['updateStruct'];
        $updatedType  = $data['updatedType'];

        $expectedValues = array(
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
            'fieldDefinitions'  => $originalType->fieldDefinitions,
        );

        $this->assertPropertiesCorrect(
            $expectedValues,
            $updatedType
        );
    }

    /**
     * Test for the updateContentTypeDraft() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::updateContentTypeDraft()
     * @expectedException \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @depends eZ\Publish\API\Repository\Tests\ContentTypeServiceTest::testUpdateContentTypeDraft
     */
    public function testUpdateContentTypeDraftThrowsInvalidArgumentExceptionDuplicateIdentifier()
    {
        $contentTypeDraft = $this->createContentTypeDraft();

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
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::updateContentTypeDraft()
     * @expectedException \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @depends eZ\Publish\API\Repository\Tests\ContentTypeServiceTest::testUpdateContentTypeDraft
     */
    public function testUpdateContentTypeDraftThrowsInvalidArgumentExceptionDuplicateRemoteId()
    {
        $contentTypeDraft = $this->createContentTypeDraft();

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
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::updateContentTypeDraft()
     * @expectedException \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @depends eZ\Publish\API\Repository\Tests\ContentTypeServiceTest::testUpdateContentTypeDraft
     */
    public function testUpdateContentTypeDraftThrowsInvalidArgumentExceptionIncorrectUser()
    {
        $this->markTestIncomplete( "Is not implemented." );
    }

    /**
     * Test for the addFieldDefinition() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::addFieldDefinition()
     * 
     */
    public function testAddFieldDefinition()
    {
        $contentTypeDraft = $this->createContentTypeDraft();

        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        // $contentTypeDraft contains a ContentTypeDraft

        $contentTypeService = $repository->getContentTypeService();

        $fieldDefCreate = $contentTypeService->newFieldDefinitionCreateStruct(
            'tags', 'string'
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
        $fieldDefCreate->validators      = array(
            new StringLengthValidatorStub(),
        );
        $fieldDefCreate->fieldSettings = array(
            'textblockheight' => 10
        );
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
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::addFieldDefinition()
     * @depends eZ\Publish\API\Repository\Tests\ContentTypeServiceTest::testAddFieldDefinition
     */
    public function testAddFieldDefinitionStructValues( array $data )
    {
        $loadedType     = $data['loadedType'];
        $fieldDefCreate = $data['fieldDefCreate'];

        foreach ( $loadedType->fieldDefinitions as $fieldDefinition )
        {
            if ( $fieldDefinition->identifier == $fieldDefCreate->identifier )
            {
                $this->assertFieldDefinitionsEqual( $fieldDefCreate, $fieldDefinition );
                return;
            }
        }

        $this->fail(
            sprintf(
                'Field definition with identifier "%s" not create.',
                $fieldDefCreate->identifier
            )
        );
    }

    /**
     * Test for the addFieldDefinition() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::addFieldDefinition()
     * @expectedException \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function testAddFieldDefinitionThrowsInvalidArgumentExceptionDuplicateFieldIdentifier()
    {
        $contentTypeDraft = $this->createContentTypeDraft();

        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        // $contentTypeDraft contains a ContentTypeDraft
        // $contentTypeDraft has a field "title"

        $contentTypeService = $repository->getContentTypeService();

        $fieldDefCreate = $contentTypeService->newFieldDefinitionCreateStruct(
            'title', 'string'
        );

        // Throws an exception
        $contentTypeService->addFieldDefinition( $contentTypeDraft, $fieldDefCreate );
        /* END: Use Case */
    }

    /**
     * Test for the removeFieldDefinition() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::removeFieldDefinition()
     * @depens eZ\Publish\API\Repository\Tests\ContentTypeServiceTest::testCreateContentType
     */
    public function testRemoveFieldDefinition()
    {
        $contentTypeDraft = $this->createContentTypeDraft();
        $draftId = $contentTypeDraft->id;

        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        // $draftId contains the ID of a content type draft
        $contentTypeService = $repository->getContentTypeService();

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
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::removeFieldDefinition()
     * @depends eZ\Publish\API\Repository\Tests\ContentTypeServiceTest::testRemoveFieldDefinition
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
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::removeFieldDefinition()
     * @expectedException \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @depends eZ\Publish\API\Repository\Tests\ContentTypeServiceTest::testRemoveFieldDefinition
     */
    public function testRemoveFieldDefinitionThrowsInvalidArgumentException()
    {
        $contentTypeDraft = $this->createContentTypeDraft();
        $draftId = $contentTypeDraft->id;

        $repository = $this->getRepository();
        /* BEGIN: Use Case */
        // $draftId contains the ID of a content type draft
        $contentTypeService = $repository->getContentTypeService();

        $contentTypeDraft = $contentTypeService->loadContentTypeDraft( $draftId );

        $bodyField = $contentTypeDraft->getFieldDefinition( 'body' );
        $contentTypeService->removeFieldDefinition( $contentTypeDraft, $bodyField );

        $loadedDraft = $contentTypeService->loadContentTypeDraft( $draftId );

        // Throws exception, sine "body" has already been removed
        $contentTypeService->removeFieldDefinition( $loadedDraft, $bodyField );
        /* END: Use Case */
    }

    /**
     * Test for the newFieldDefinitionUpdateStruct() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::newFieldDefinitionUpdateStruct()
     * 
     */
    public function testNewFieldDefinitionUpdateStruct()
    {
        $repository = $this->getRepository();
        /* BEGIN: Use Case */
        // $draftId contains the ID of a content type draft
        $contentTypeService = $repository->getContentTypeService();

        $updateStruct = $contentTypeService->newFieldDefinitionUpdateStruct();
        /* END: Use Case */

        $this->assertInstanceOf(
            '\\eZ\\Publish\\API\\Repository\\Values\\ContentType\\FieldDefinitionUpdateStruct',
            $updateStruct
        );
    }

    /**
     * Test for the updateFieldDefinition() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::updateFieldDefinition()
     * 
     */
    public function testUpdateFieldDefinition()
    {
        $contentTypeDraft = $this->createContentTypeDraft();
        $draftId = $contentTypeDraft->id;

        $repository = $this->getRepository();
        /* BEGIN: Use Case */
        // $draftId contains the ID of a content type draft
        $contentTypeService = $repository->getContentTypeService();

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
        $bodyUpdateStruct->validators      = array();
        $bodyUpdateStruct->fieldSettings = array(
            'textblockheight' => 60
        );
        $bodyUpdateStruct->isSearchable = false;

        $contentTypeService->updateFieldDefinition(
            $contentTypeDraft,
            $bodyField,
            $bodyUpdateStruct
        );
        /* END: Use Case */

        $loadedDraft = $contentTypeService->loadContentTypeDraft( $draftId );
        $this->assertInstanceOf(
            'eZ\\Publish\\API\\Repository\\Values\\ContentType\\FieldDefinition',
            ( $loadedField = $loadedDraft->getFieldDefinition( 'blog-body' ) )
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
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::updateFieldDefinition()
     * @depends eZ\Publish\API\Repository\Tests\ContentTypeServiceTest::testUpdateFieldDefinition
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
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::updateFieldDefinition()
     * @expectedException \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function testUpdateFieldDefinitionThrowsInvalidArgumentException()
    {
        $contentTypeDraft = $this->createContentTypeDraft();
        $draftId = $contentTypeDraft->id;

        $repository = $this->getRepository();
        /* BEGIN: Use Case */
        // $draftId contains the ID of a content type draft
        $contentTypeService = $repository->getContentTypeService();

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
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::updateFieldDefinition()
     * @expectedException \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function testUpdateFieldDefinitionThrowsInvalidArgumentExceptionForUndefinedField()
    {
        $contentTypeDraft = $this->createContentTypeDraft();
        $draftId = $contentTypeDraft->id;

        $repository = $this->getRepository();
        /* BEGIN: Use Case */
        // $draftId contains the ID of a content type draft
        $contentTypeService = $repository->getContentTypeService();

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

    /**
     * Test for the publishContentTypeDraft() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::publishContentTypeDraft()
     * 
     */
    public function testPublishContentTypeDraft()
    {
        $repository = $this->getRepository();

        $contentTypeDraft = $this->createContentTypeDraft();
        $draftId = $contentTypeDraft->id;

        /* BEGIN: Use Case */
        // $contentTypeDraft contains a ContentTypeDraft
        $contentTypeService = $repository->getContentTypeService();
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
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::publishContentTypeDraft()
     * @expectedException \eZ\Publish\API\Repository\Exceptions\BadStateException
     */
    public function testPublishContentTypeDraftThrowsBadStateException()
    {
        $repository = $this->getRepository();

        $contentTypeDraft = $this->createContentTypeDraft();
        $draftId = $contentTypeDraft->id;

        /* BEGIN: Use Case */
        // $contentTypeDraft contains a ContentTypeDraft
        $contentTypeService = $repository->getContentTypeService();
        $contentTypeDraft = $contentTypeService->loadContentTypeDraft( $draftId );

        $contentTypeService->publishContentTypeDraft( $contentTypeDraft );

        // Throws exception, since no draft exists anymore
        $contentTypeService->publishContentTypeDraft( $contentTypeDraft );
        /* END: Use Case */
    }

    /**
     * Test for the loadContentType() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::loadContentType()
     * 
     */
    public function testLoadContentType()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();
        // Loads the standard "user_group" type
        $userGroupType = $contentTypeService->loadContentType( 3 );
        /* END: Use Case */

        $this->assertInstanceOf(
            'eZ\\Publish\\API\\Repository\\Values\\ContentType\\ContentType',
            $userGroupType
        );

        return $userGroupType;
    }

    /**
     * Test for the loadContentType() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::loadContentType()
     * @depends eZ\Publish\API\Repository\Tests\ContentTypeServiceTest::testLoadContentType
     */
    public function testLoadContentTypeStructValues( $userGroupType )
    {
        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        $this->assertPropertiesCorrect(
            array(
                'id'               => 3,
                'status'           => 0,
                'identifier'       => 'user_group',
                'creationDate'     => new \DateTime( '@1024392098' ),
                'modificationDate' => new \DateTime( '@1048494743' ),
                'creatorId'        => 14,
                'modifierId'       => 14,
                'remoteId'         => '25b4268cdcd01921b808a0d854b877ef',
                'names'            => array(
                    'eng-US' => 'User group',
                ),
                'descriptions' => array(
                ),
                'nameSchema'             => '<name>',
                'isContainer'            => true,
                'mainLanguageCode'       => 'eng-US',
                'defaultAlwaysAvailable' => true,
                'defaultSortField'       => 1,
                'defaultSortOrder'       => 1,
                'fieldDefinitions'       => array(
                    6 => new \eZ\Publish\API\Repository\Tests\Stubs\Values\ContentType\FieldDefinitionStub(
                        array(
                            'id'                  => 6,
                            'identifier'          => 'name',
                            'fieldGroup'          => '',
                            'position'            => 1,
                            'fieldTypeIdentifier' => 'ezstring',
                            'isTranslatable'      => true,
                            'isRequired'          => true,
                            'isInfoCollector'     => false,
                            'isSearchable'        => true,
                            'defaultValue'        => null,
                            'names'               => array(
                                'eng-US' => 'Name',
                            ),
                            'descriptions' => array(
                                0 => '',
                            ),
                            'fieldSettings' => array(
                            ),
                            'validators' => array(
                            ),
                        )
                    ),
                    7 => new \eZ\Publish\API\Repository\Tests\Stubs\Values\ContentType\FieldDefinitionStub(
                        array(
                            'id'                  => 7,
                            'identifier'          => 'description',
                            'fieldGroup'          => '',
                            'position'            => 2,
                            'fieldTypeIdentifier' => 'ezstring',
                            'isTranslatable'      => true,
                            'isRequired'          => false,
                            'isInfoCollector'     => false,
                            'isSearchable'        => true,
                            'defaultValue'        => null,
                            'names'               => array(
                                'eng-US' => 'Description',
                            ),
                            'descriptions' => array(
                                0 => '',
                            ),
                            'fieldSettings' => array(
                            ),
                            'validators' => array(
                            ),
                        )
                    ),
                ),
                'contentTypeGroups' => array(
                    0 => $contentTypeService->loadContentTypeGroup( 2 )
                ),
            ),
            $userGroupType
        );
    }

    /**
     * Test for the loadContentType() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::loadContentType()
     * @expectedException \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function testLoadContentTypeThrowsNotFoundException()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        // Throws exception, since type with ID 2342 does not exist
        $userGroupType = $contentTypeService->loadContentType( 2342 );
        /* END: Use Case */
    }

    /**
     * Test for the loadContentTypeByIdentifier() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::loadContentTypeByIdentifier()
     * @dep_ends eZ\Publish\API\Repository\Tests\RepositoryTest::testGetContentTypeService;
     */
    public function testLoadContentTypeByIdentifier()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $articleType = $contentTypeService->loadContentTypeByIdentifier( 'article' );
        /* END: Use Case */

        $this->assertInstanceOf(
            '\\eZ\\Publish\\API\\Repository\\Values\\ContentType\\ContentType',
            $articleType
        );

        return $articleType;
    }

    /**
     * Test for the loadContentTypeByIdentifier() method.
     *
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::loadContentTypeByIdentifier()
     * @depends eZ\Publish\API\Repository\Tests\ContentTypeServiceTest::testLoadContentTypeByIdentifier
     */
    public function testLoadContentTypeByIdentifierReturnsCorrectInstance( $contentType )
    {
        $repository         = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        $this->assertEquals(
            $contentTypeService->loadContentType( $contentType->id ),
            $contentType
        );
    }

    /**
     * Test for the loadContentTypeByIdentifier() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::loadContentTypeByIdentifier()
     * @expectedException \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @depends eZ\Publish\API\Repository\Tests\ContentTypeServiceTest::testLoadContentTypeByIdentifier
     */
    public function testLoadContentTypeByIdentifierThrowsNotFoundException()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        // Throws an exception, since no type with this identifier exists
        $contentTypeService->loadContentTypeByIdentifier( 'sindelfingen' );
        /* END: Use Case */
    }

    /**
     * Test for the loadContentTypeByRemoteId() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::loadContentTypeByRemoteId()
     * 
     */
    public function testLoadContentTypeByRemoteId()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        // Loads the standard "user_group" type
        $userGroupType = $contentTypeService->loadContentTypeByRemoteId(
            '25b4268cdcd01921b808a0d854b877ef'
        );
        /* END: Use Case */

        $this->assertInstanceOf(
            '\\eZ\\Publish\\API\\Repository\\Values\\ContentType\\ContentType',
            $userGroupType
        );

        return $userGroupType;
    }

    /**
     * Test for the loadContentTypeByRemoteId() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::loadContentTypeByRemoteId()
     * @depends eZ\Publish\API\Repository\Tests\ContentTypeServiceTest::testLoadContentTypeByRemoteId
     */
    public function testLoadContentTypeByRemoteIdReturnsCorrectInstance( $contentType )
    {
        $repository         = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        $this->assertEquals(
            $contentTypeService->loadContentType( $contentType->id ),
            $contentType
        );
    }

    /**
     * Test for the loadContentTypeByRemoteId() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::loadContentTypeByRemoteId()
     * @expectedException \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function testLoadContentTypeByRemoteIdThrowsNotFoundException()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        // Throws an exception, since no type with this remote ID exists
        $contentTypeService->loadContentTypeByRemoteId( 'not-exists' );
        /* END: Use Case */
    }

    /**
     * Test for the loadContentTypes() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::loadContentTypes()
     * 
     */
    public function testLoadContentTypes()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $contentTypeGroup = $contentTypeService->loadContentTypeGroup( 2 );

        // Loads all types from content type group "Users"
        $types = $contentTypeService->loadContentTypes( $contentTypeGroup );
        /* END: Use Case */

        $this->assertInternalType( 'array', $types );

        return $types;
    }

    /**
     * Test for the loadContentTypes() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::loadContentTypes()
     * @depends eZ\Publish\API\Repository\Tests\ContentTypeServiceTest::testLoadContentTypes
     */
    public function testLoadContentTypesContent( array $types )
    {
        $repository         = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        $this->assertEquals(
            array(
                $contentTypeService->loadContentType( 3 ),
                $contentTypeService->loadContentType( 4 ),
            ),
            $types
        );
    }

    /**
     * Test for the createContentTypeDraft() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::createContentTypeDraft()
     * 
     */
    public function testCreateContentTypeDraft()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $commentType = $contentTypeService->loadContentTypeByIdentifier( 'comment' );

        $commentTypeDraft = $contentTypeService->createContentTypeDraft( $commentType );
        /* END: Use Case */

        $this->assertInstanceOf(
            'eZ\\Publish\\API\\Repository\\Values\\ContentType\\ContentTypeDraft',
            $commentTypeDraft
        );

        return array(
            'originalType' => $commentType,
            'typeDraft'    => $commentTypeDraft,
        );
    }

    /**
     * Test for the createContentTypeDraft() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::createContentTypeDraft()
     * @depends eZ\Publish\API\Repository\Tests\ContentTypeServiceTest::testCreateContentTypeDraft
     */
    public function testCreateContentTypeDraftStructValues( array $data )
    {
        $originalType = $data['originalType'];
        $typeDraft    = $data['typeDraft'];

        $this->assertStructPropertiesCorrect(
            $originalType,
            $typeDraft,
            array(
                'id',
                'names',
                'descriptions',
                'identifier',
                'creationDate',
                'modificationDate',
                'creatorId',
                'modifierId',
                'remoteId',
                'urlAliasSchema',
                'nameSchema',
                'isContainer',
                'mainLanguageCode',
                'defaultAlwaysAvailable',
                'defaultSortField',
                'defaultSortOrder',
                'contentTypeGroups',
                'fieldDefinitions',
            )
        );

        $this->assertEquals(
            ContentType::STATUS_DRAFT,
            $typeDraft->status
        );
    }

    /**
     * Test for the createContentTypeDraft() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::createContentTypeDraft()
     * @expectedException \eZ\Publish\API\Repository\Exceptions\BadStateException
     */
    public function testCreateContentTypeDraftThrowsBadStateException()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $commentType = $contentTypeService->loadContentTypeByIdentifier( 'comment' );

        $commentTypeDraft = $contentTypeService->createContentTypeDraft( $commentType );

        // Throws exception, since type draft already exists
        $invalidTypeDraft = $contentTypeService->createContentTypeDraft( $commentType );
        /* END: Use Case */
    }

    /**
     * Test for the deleteContentType() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::deleteContentType()
     * 
     */
    public function testDeleteContentType()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $commentType = $contentTypeService->loadContentTypeByIdentifier( 'comment' );

        $contentTypeService->deleteContentType( $commentType );
        /* END: Use Case */

        try
        {
            $contentTypeService->loadContentType( $commentType->id );
            $this->fail( 'Content type could be loaded after delete.' );
        }
        catch( Exceptions\NotFoundException $e )
        {
            // All fine
        }
    }

    /**
     * Test for the deleteContentType() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::deleteContentType()
     * @expectedException \eZ\Publish\API\Repository\Exceptions\BadStateException
     */
    public function testDeleteContentTypeThrowsBadStateException()
    {
        // TODO: Needs existsing content objects
        $this->markTestIncomplete( "@TODO: Test for ContentTypeService::deleteContentType() is not implemented." );
    }

    /**
     * Test for the copyContentType() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::copyContentType()
     * 
     */
    public function testCopyContentType()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $commentType = $contentTypeService->loadContentTypeByIdentifier( 'comment' );

        // Complete copy of the "comment" type
        $copiedType = $contentTypeService->copyContentType( $commentType );
        /* END: Use Case */

        $this->assertInstanceOf(
            '\\eZ\\Publish\\API\\Repository\\Values\\ContentType\\ContentType',
            $copiedType
        );

        return array(
            'originalType' => $commentType,
            'copiedType'   => $copiedType,
        );
    }

    /**
     * Test for the copyContentType() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::copyContentType()
     * @depends eZ\Publish\API\Repository\Tests\ContentTypeServiceTest::testCopyContentType
     */
    public function testCopyContentTypeStructValues( array $data )
    {
        $originalType = $data['originalType'];
        $copiedType   = $data['copiedType'];

        $this->assertStructPropertiesCorrect(
            $originalType,
            $copiedType,
            array(
                'names',
                'descriptions',
                'creatorId',
                'modifierId',
                'urlAliasSchema',
                'nameSchema',
                'isContainer',
                'mainLanguageCode',
                'contentTypeGroups',
            )
        );

        $this->assertNotEquals(
            $originalType->id,
            $copiedType->id
        );
        $this->assertNotEquals(
            $originalType->remoteId,
            $copiedType->remoteId
        );
        $this->assertNotEquals(
            $originalType->identifier,
            $copiedType->identifier
        );
        $this->assertNotEquals(
            $originalType->creationDate,
            $copiedType->creationDate
        );
        $this->assertNotEquals(
            $originalType->modificationDate,
            $copiedType->modificationDate
        );

        foreach ( $originalType->fieldDefinitions as $originalFieldDefinition )
        {
            $copiedFieldDefinition = $copiedType->getFieldDefinition(
                $originalFieldDefinition->identifier
            );

            $this->assertStructPropertiesCorrect(
                $originalFieldDefinition,
                $copiedFieldDefinition,
                array(
                    'identifier',
                    'names',
                    'descriptions',
                    'fieldGroup',
                    'position',
                    'fieldTypeIdentifier',
                    'isTranslatable',
                    'isRequired',
                    'isInfoCollector',
                    'validators',
                    'defaultValue',
                    'isSearchable',
                )
            );
            $this->assertNotEquals(
                $originalFieldDefinition->id,
                $copiedFieldDefinition->id
            );
        }
    }

    /**
     * Test for the copyContentType() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::copyContentType($contentType, $user)
     * 
     */
    public function testCopyContentTypeWithSecondParameter()
    {
        // TODO: Needs UserService
        $this->markTestIncomplete( "@TODO: Test for ContentTypeService::copyContentType() is not implemented." );
    }

    /**
     * Test for the assignContentTypeGroup() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::assignContentTypeGroup()
     * 
     */
    public function testAssignContentTypeGroup()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

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
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::assignContentTypeGroup()
     * @expectedException \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function testAssignContentTypeGroupThrowsInvalidArgumentException()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

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
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::unassignContentTypeGroup()
     * 
     */
    public function testUnassignContentTypeGroup()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

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
                        $groupToUnassign->id
                    )
                );
            }
        }
    }

    /**
     * Test for the unassignContentTypeGroup() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::unassignContentTypeGroup()
     * @expectedException \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function testUnassignContentTypeGroupThrowsInvalidArgumentException()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $folderType = $contentTypeService->loadContentTypeByIdentifier( 'folder' );
        $notAssignedGroup = $contentTypeService->loadContentTypeGroupByIdentifier( 'Media' );

        // Throws an exception, since "Media" group is not assigned to "folder"
        $contentTypeService->unassignContentTypeGroup( $folderType, $notAssignedGroup );
        /* END: Use Case */
    }

    /**
     * Test for the unassignContentTypeGroup() method.
     *
     * @return void
     * @see \eZ\Publish\API\Repository\ContentTypeService::unassignContentTypeGroup()
     * @expectedException \eZ\Publish\API\Repository\Exceptions\BadStateException
     */
    public function testUnassignContentTypeGroupThrowsBadStateException()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $folderType     = $contentTypeService->loadContentTypeByIdentifier( 'folder' );
        $assignedGroups = $folderType->contentTypeGroups;

        foreach ( $assignedGroups as $assignedGroup )
        {
            // Throws an exception, when last group is to be removed
            $contentTypeService->unassignContentTypeGroup( $folderType, $assignedGroup );
        }
        /* END: Use Case */
    }
}