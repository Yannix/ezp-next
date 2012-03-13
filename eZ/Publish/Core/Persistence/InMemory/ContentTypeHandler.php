<?php
/**
 * File containing the ContentHandler implementation
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 *
 */

namespace eZ\Publish\Core\Persistence\InMemory;
use eZ\Publish\SPI\Persistence\Content\Type\Handler as ContentTypeHandlerInterface,
    eZ\Publish\SPI\Persistence\Content\Type,
    eZ\Publish\SPI\Persistence\Content\Type\CreateStruct,
    eZ\Publish\SPI\Persistence\Content\Type\UpdateStruct,
    eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition,
    eZ\Publish\SPI\Persistence\Content\Type\Group\CreateStruct as GroupCreateStruct,
    eZ\Publish\SPI\Persistence\Content\Type\Group\UpdateStruct as GroupUpdateStruct,
    eZ\Publish\SPI\Persistence\Content\Type\Group,
    eZ\Publish\Core\Base\Exceptions\NotFound,
    ezp\Base\Exception\BadRequest,
    eZ\Publish\Core\Persistence\InMemory\Handler,
    eZ\Publish\Core\Persistence\InMemory\Backend,
    RuntimeException;

/**
 * @see \eZ\Publish\SPI\Persistence\Content\Type\Handler
 *
 * @todo Validate $status arguments
 */
class ContentTypeHandler implements ContentTypeHandlerInterface
{
    /**
     * @var Handler
     */
    protected $handler;

    /**
     * @var Backend
     */
    protected $backend;

    /**
     * Setups current handler instance with reference to Handler object that created it.
     *
     * @param Handler $handler
     * @param Backend $backend The storage engine backend
     */
    public function __construct( Handler $handler, Backend $backend )
    {
        $this->handler = $handler;
        $this->backend = $backend;
    }

    /**
     * @param \eZ\Publish\SPI\Persistence\Content\Type\Group\CreateStruct $group
     * @return \eZ\Publish\SPI\Persistence\Content\Type\Group
     */
    public function createGroup( GroupCreateStruct $group )
    {
        $groupArr = (array)$group;
        return $this->backend->create( 'Content\\Type\\Group', $groupArr );
    }

    /**
     * @param \eZ\Publish\SPI\Persistence\Content\Type\Group\UpdateStruct $group
     */
    public function updateGroup( GroupUpdateStruct $group )
    {
        $groupArr = (array)$group;
        $this->backend->update( 'Content\\Type\\Group', $groupArr['id'], $groupArr );
    }

    /**
     * @param mixed $groupId
     * @todo Throw exception if group is not found, also if group contains types
     */
    public function deleteGroup( $groupId )
    {
        $this->backend->delete( 'Content\\Type\\Group', $groupId );

        // Remove group id from content types
        $types = $this->backend->find( 'Content\\Type', array( 'groupIds' => $groupId ) );
        foreach ( $types as $type )
        {
            $update = false;
            foreach ( $type->groupIds as $key => $contentTypeGroupId )
            {
                if ( $contentTypeGroupId == $groupId )
                {
                    unset( $type->groupIds[$key] );
                    $update = true;
                }
            }

            if ( $update )
            {
                // @todo If groupIds is empty, content type and content of that type should be deleted?
                $this->backend->update(
                    'Content\\Type',
                    $type->id,
                    array( 'groupIds' => $type->groupIds )
                );
            }
        }
    }

    /**
     * @param mixed $groupId
     * @return \eZ\Publish\SPI\Persistence\Content\Type\Group
     * @throws \ezp\Base\Exception\NotFound If type group with id is not found
     */
    public function loadGroup( $groupId )
    {
        return $this->backend->load( 'Content\\Type\\Group', $groupId );
    }

    /**
     * @param string $identifier
     * @return \eZ\Publish\SPI\Persistence\Content\Type\Group
     * @throws \ezp\Base\Exception\NotFound If type group with id is not found
     */
    public function loadGroupByIdentifier( $identifier )
    {
        $group = $this->backend->find( 'Content\\Type\\Group', array( 'identifier' => $identifier ) );
        if ( !$group )
            throw new NotFound( 'Content\\Type\\Group', $identifier );

        return $group[0];
    }

    /**
     * @return \eZ\Publish\SPI\Persistence\Content\Type\Group[]
     */
    public function loadAllGroups()
    {
        return $this->backend->find( 'Content\\Type\\Group', array() );
    }

    /**
     * @param mixed $groupId
     * @param int $status One of Type::STATUS_DEFINED|Type::STATUS_DRAFT|Type::STATUS_MODIFIED
     * @return \eZ\Publish\SPI\Persistence\Content\Type[]
     */
    public function loadContentTypes( $groupId, $status = Type::STATUS_DEFINED )
    {
        return $this->backend->find(
            'Content\\Type',
            array( 'groupIds' => $groupId, 'status' => $status ),
            array(
                'fieldDefinitions' => array(
                    'type' => 'Content\\Type\\FieldDefinition',
                    'match' => array( '_typeId' => 'id', '_status' => 'status' )
                )
            )
        );
    }

    /**
     * Load a content type by id and status
     *
     * @param mixed $contentTypeId
     * @param int $status One of Type::STATUS_DEFINED|Type::STATUS_DRAFT|Type::STATUS_MODIFIED
     * @return \eZ\Publish\SPI\Persistence\Content\Type
     * @throws \ezp\Base\Exception\NotFound If type with provided status is not found
     */
    public function load( $contentTypeId, $status = Type::STATUS_DEFINED )
    {
        $type = $this->backend->find(
            'Content\\Type',
            array( 'id' => $contentTypeId, 'status' => $status ),
            array(
                'fieldDefinitions' => array(
                    'type' => 'Content\\Type\\FieldDefinition',
                    'match' => array( '_typeId' => 'id', '_status' => 'status' )
                )
            )
        );

        if ( !$type )
            throw new NotFound( 'Content\\Type', "{$contentTypeId}' and status '{$status}" );

        return $type[0];
    }

    /**
     * Load a (defined) content type by identifier
     *
     * @param string $identifier
     * @return \eZ\Publish\SPI\Persistence\Content\Type
     * @throws \ezp\Base\Exception\NotFound If defined type is not found
     */
    public function loadByIdentifier( $identifier )
    {
        $type = $this->backend->find(
            'Content\\Type',
            array( 'identifier' => $identifier, 'status' => Type::STATUS_DEFINED ),
            array(
                'fieldDefinitions' => array(
                    'type' => 'Content\\Type\\FieldDefinition',
                    'match' => array( '_typeId' => 'id', '_status' => 'status' )
                )
            )
        );

        if ( !$type )
            throw new NotFound( 'Content\\Type', "{$identifier}' and status '" . Type::STATUS_DEFINED );

        return $type[0];
    }

    /**
     * Load a (defined) content type by remote id
     *
     * @param mixed $remoteId
     * @return \eZ\Publish\SPI\Persistence\Content\Type
     * @throws \ezp\Base\Exception\NotFound If defined type is not found
     */
    public function loadByRemoteId( $remoteId )
    {
        $type = $this->backend->find(
            'Content\\Type',
            array( 'remoteId' => $remoteId, 'status' => Type::STATUS_DEFINED ),
            array(
                'fieldDefinitions' => array(
                    'type' => 'Content\\Type\\FieldDefinition',
                    'match' => array( '_typeId' => 'id', '_status' => 'status' )
                )
            )
        );

        if ( !$type )
            throw new NotFound( 'Content\\Type', "{$remoteId}' and status '" . Type::STATUS_DEFINED );

        return $type[0];
    }

    /**
     * @param \eZ\Publish\SPI\Persistence\Content\Type\CreateStruct $contentType
     * @return \eZ\Publish\SPI\Persistence\Content\Type
     */
    public function create( CreateStruct $contentType )
    {
        $contentTypeArr = (array)$contentType;
        unset( $contentTypeArr['fieldDefinitions'] );
        $contentTypeObj = $this->backend->create( 'Content\\Type', $contentTypeArr );
        foreach ( $contentType->fieldDefinitions as $field )
        {
            $contentTypeObj->fieldDefinitions[] = $this->backend->create(
                'Content\\Type\\FieldDefinition',
                array( '_typeId' => $contentTypeObj->id, '_status' => $contentTypeObj->status ) + (array)$field
            );
        }
        return $contentTypeObj;
    }

    /**
     * @param mixed $typeId
     * @param int $status One of Type::STATUS_DEFINED|Type::STATUS_DRAFT|Type::STATUS_MODIFIED
     * @param \eZ\Publish\SPI\Persistence\Content\Type\UpdateStruct $contentType
     */
    public function update( $typeId, $status, UpdateStruct $contentType )
    {
        $contentTypeArr = (array)$contentType;
        $this->backend->updateByMatch(
            'Content\\Type',
            array( 'id' => $typeId, 'status' => $status ),
            $contentTypeArr
        );
    }

    /**
     * @param mixed $contentTypeId
     * @param int $status One of Type::STATUS_DEFINED|Type::STATUS_DRAFT|Type::STATUS_MODIFIED
     */
    public function delete( $contentTypeId, $status )
    {
        $this->backend->deleteByMatch(
            'Content\\Type',
            array( 'id' => $contentTypeId, 'status' => $status )
        );
        $this->backend->deleteByMatch(
            'Content\\Type\\FieldDefinition',
            array( '_typeId' => $contentTypeId, '_status' => $status )
        );
    }

    /**
     * Creates a draft of existing defined content type
     *
     * Updates modified date, sets $modifierId and status to Type::STATUS_DRAFT on the new returned draft.
     *
     * @param mixed $modifierId
     * @param mixed $contentTypeId
     * @return \eZ\Publish\SPI\Persistence\Content\Type
     * @throws \ezp\Base\Exception\NotFound If type with defined status is not found
     */
    public function createDraft( $modifierId, $contentTypeId )
    {
        $contentType = $this->load( $contentTypeId );
        $contentType->modified = time();
        $contentType->modifierId = $modifierId;
        $contentType->status = Type::STATUS_DRAFT;

        $contentTypeArr = (array)$contentType;
        unset( $contentTypeArr['fieldDefinitions'] );

        $contentTypeObj = $this->backend->create( 'Content\\Type', $contentTypeArr, false );
        foreach ( $contentType->fieldDefinitions as $field )
        {
            $contentTypeObj->fieldDefinitions[] = $this->backend->create(
                'Content\\Type\\FieldDefinition',
                array( '_typeId' => $contentTypeObj->id, '_status' => $contentTypeObj->status ) + (array)$field
            );
        }
        return $contentTypeObj;
    }

    /**
     * Copy a Type incl fields and group-relations from a given status to a new Type with status {@link Type::STATUS_DRAFT}
     *
     * New Content Type will have $userId as creator / modifier, created / modified should be updated, new remoteId
     * and identifier should be appended with '_' and new remoteId or another unique number.
     *
     * @param mixed $userId
     * @param mixed $contentTypeId
     * @param int $status One of Type::STATUS_DEFINED|Type::STATUS_DRAFT|Type::STATUS_MODIFIED
     * @return \eZ\Publish\SPI\Persistence\Content\Type
     * @throws \ezp\Base\Exception\NotFound If user or type with provided status is not found
     */
    public function copy( $userId, $contentTypeId, $status )
    {
        $type = $this->load( $contentTypeId, $status );

        // @todo Validate $userId
        $struct = new CreateStruct();
        foreach ( $struct as $property => $value )
        {
            $struct->$property = $type->$property;
        }
        $struct->created = $struct->modified = time();
        $struct->creatorId = $struct->modifierId = $userId;
        $struct->status = Type::STATUS_DRAFT;
        $struct->identifier .= '_' . ( $struct->remoteId = md5( uniqid( get_class( $struct ), true ) ) );
        return $this->create( $struct );// this takes care of resetting _typeId and _status on fields
    }

    /**
     * Unlink a content type group from a content type
     *
     * @param mixed $groupId
     * @param mixed $contentTypeId
     * @param int $status One of Type::STATUS_DEFINED|Type::STATUS_DRAFT|Type::STATUS_MODIFIED
     * @throws \ezp\Base\Exception\NotFound If group or type with provided status is not found
     * @throws \ezp\Base\Exception\BadRequest If type is not part of group or group is last on type (delete type instead)
     */
    public function unlink( $groupId, $contentTypeId, $status )
    {
        $list = $this->backend->find( 'Content\\Type', array( 'id' => $contentTypeId, 'status' => $status ) );
        if ( !isset( $list[0] ) )
            throw new NotFound( 'Content\\Type', "{$contentTypeId}' and status '{$status}" );

        $type = $list[0];
        if ( !in_array( $groupId, $type->groupIds ) )
            throw new BadRequest( "group:{$groupId}", "groupIds on Type:{$contentTypeId}" );

        if ( !isset( $type->groupIds[1] ) )
            throw new BadRequest(
                'groups',
                "Type: {$contentTypeId}.\nCan not remove last Group: {$groupId}, delete Type instead"
            );

        if ( !$this->backend->load( 'Content\\Type\\Group', $groupId ) )
            throw new NotFound( 'Content\\Type\\Group', $groupId );

        $this->backend->updateByMatch(
            'Content\\Type',
            array( 'id' => $contentTypeId, 'status' => $status ),
            array( 'groupIds' => array_values( array_diff( $type->groupIds, array( $groupId ) ) ) )
        );
    }

    /**
     * Link a content type group with a content type
     *
     * @param mixed $groupId
     * @param mixed $contentTypeId
     * @param int $status One of Type::STATUS_DEFINED|Type::STATUS_DRAFT|Type::STATUS_MODIFIED
     * @throws \ezp\Base\Exception\NotFound If group or type with provided status is not found
     * @throws \ezp\Base\Exception\BadRequest If type is already part of group
     */
    public function link( $groupId, $contentTypeId, $status )
    {
        $list = $this->backend->find( 'Content\\Type', array( 'id' => $contentTypeId, 'status' => $status ) );
        if ( !isset( $list[0] ) )
            throw new NotFound( 'Content\\Type', "{$contentTypeId}' and status '{$status}" );

        $type = $list[0];
        if ( in_array( $groupId, $type->groupIds ) )
            throw new BadRequest( "group:{$groupId}", "groupIds on Type:{$contentTypeId}" );

        if ( !$this->backend->load( 'Content\\Type\\Group', $groupId ) )
            throw new NotFound( 'Content\\Type\\Group', $groupId );

        $this->backend->updateByMatch(
            'Content\\Type',
            array( 'id' => $contentTypeId, 'status' => $status ),
            array( 'groupIds' => array_merge( $type->groupIds, array( $groupId ) ) )
        );
    }

    /**
     * Adds a new field definition to an existing Type.
     *
     * This method creates a new version of the Type with the $fieldDefinition
     * added. It does not update existing content objects depending on the
     * field (default) values.
     *
     * @param mixed $contentTypeId
     * @param int $status One of Type::STATUS_DEFINED|Type::STATUS_DRAFT|Type::STATUS_MODIFIED
     * @param \eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition $fieldDefinition
     * @return \eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition
     * @throws \ezp\Base\Exception\NotFound If type is not found
     * @todo Add FieldDefintion\CreateStruct?
     */
    public function addFieldDefinition( $contentTypeId, $status, FieldDefinition $fieldDefinition )
    {
        $list = $this->backend->find( 'Content\\Type', array( 'id' => $contentTypeId, 'status' => $status ) );
        if ( !isset( $list[0] ) )
            throw new NotFound( 'Content\\Type', "{$contentTypeId}' and status '{$status}" );

        $fieldDefinitionArr = (array)$fieldDefinition;
        $fieldDefinitionArr['_typeId'] = $contentTypeId;
        $fieldDefinitionArr['_status'] = $status;

        return $this->backend->create( 'Content\\Type\\FieldDefinition', $fieldDefinitionArr );
    }

    /**
     * Removes a field definition from an existing Type.
     *
     * This method creates a new version of the Type with the field definition
     * referred to by $fieldDefinitionId removed. It does not update existing
     * content objects depending on the field (default) values.
     *
     * @param mixed $contentTypeId
     * @param int $status One of Type::STATUS_DEFINED|Type::STATUS_DRAFT|Type::STATUS_MODIFIED
     * @param mixed $fieldDefinitionId
     * @return void
     * @throws \ezp\Base\Exception\NotFound If field is not found
     * @todo Add FieldDefintion\UpdateStruct?
     */
    public function removeFieldDefinition( $contentTypeId, $status, $fieldDefinitionId )
    {
        $this->backend->deleteByMatch(
            'Content\\Type\\FieldDefinition',
            array(
                '_typeId' => $contentTypeId,
                '_status' => $status,
                'id' => $fieldDefinitionId,
            )
        );
    }

    /**
     * This method updates the given $fieldDefinition on a Type.
     *
     * This method creates a new version of the Type with the updated
     * $fieldDefinition. It does not update existing content objects depending
     * on the
     * field (default) values.
     *
     * @param mixed $contentTypeId
     * @param int $status One of Type::STATUS_DEFINED|Type::STATUS_DRAFT|Type::STATUS_MODIFIED
     * @param \eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition $fieldDefinition
     * @return void
     * @throws \ezp\Base\Exception\NotFound If field is not found
     */
    public function updateFieldDefinition( $contentTypeId, $status, FieldDefinition $fieldDefinition )
    {
        $fieldDefinitionArr = (array)$fieldDefinition;
        $updated = $this->backend->updateByMatch(
            'Content\\Type\\FieldDefinition',
            array(
                '_typeId' => $contentTypeId,
                '_status' => $status,
                'id' => $fieldDefinition->id,
            ),
            $fieldDefinitionArr
        );
        if ( !$updated )
            throw new NotFound( 'Content\\Type\\FieldDefinition', $fieldDefinition->id );
    }

    /**
     * Update content objects
     *
     * Updates content objects, depending on the changed field definitions.
     *
     * A content type has a state which tells if its content objects yet have
     * been adapted.
     *
     * Flags the content type as updated.
     *
     * @param mixed $contentTypeId
     * @return void
     * @throws \ezp\Base\Exception\NotFound If type with $contentTypeId and Type::STATUS_DRAFT is not found
     */
    public function publish( $contentTypeId )
    {
        $draftType = $this->load( $contentTypeId, Type::STATUS_DRAFT );
        try
        {
            $publishedType = $this->load( $contentTypeId );
        }
        catch ( NotFound $e )
        {
            // No published version of type, jump to last section where draft is promoted to defined
            GOTO updateType;
        }

        // @todo update content based on new type data change (field/scheme changes from $publishedType to $draftType)
        $this->backend->deleteByMatch(
            'Content\\Type',
            array( 'id' => $contentTypeId, 'status' => Type::STATUS_DEFINED )
        );
        $this->backend->deleteByMatch(
            'Content\\Type\\FieldDefinition',
            array( '_typeId' => $contentTypeId, '_status' => Type::STATUS_DEFINED )
        );

        updateType:
        {
            $this->backend->updateByMatch(
                'Content\\Type',
                array( 'id' => $contentTypeId, 'status' => Type::STATUS_DRAFT ),
                array( 'status' => Type::STATUS_DEFINED )
            );
            $this->backend->updateByMatch(
                'Content\\Type\\FieldDefinition',
                array(
                    '_typeId' => $contentTypeId,
                    '_status' => Type::STATUS_DRAFT,
                ),
                array( '_status' => Type::STATUS_DEFINED )
            );
        }
    }
}
