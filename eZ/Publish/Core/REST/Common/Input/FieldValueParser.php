<?php
/**
 * File containing the Input FieldValueParser class
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\Core\REST\Common\Input;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\FieldTypeService;

class FieldValueParser
{
    /**
     * @var eZ\Publish\API\Repository\ContentService
     */
    protected $contentService;

    /**
     * @var eZ\Publish\API\Repository\ContentTypeService
     */
    protected $contentTypeService;

    /**
     * @var eZ\Publish\API\Repository\FieldTypeService
     */
    protected $fieldTypeService;

    /**
     * @param eZ\Publish\API\Repository\ContentService $contentService
     * @param eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     * @param eZ\Publish\API\Repository\FieldTypeService $fieldTypeService
     */
    public function __construct( ContentService $contentService, ContentTypeService $contentTypeService, FieldTypeService $fieldTypeService )
    {
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->fieldTypeService = $fieldTypeService;
    }

    /**
     * Parses the given $value for the field $fieldDefIdentifier in the content
     * identified by $contentInfoId
     *
     * @param string $contentInfoId
     * @param string $fieldDefIdentifier
     * @param mixed $value
     * @return mixed
     */
    public function parseFieldValue( $contentInfoId, $fieldDefIdentifier, $value )
    {
        $contentInfo = $this->contentService->loadContentInfo( $contentInfoId );
        $contentType = $this->contentTypeService->loadContentType( $contentInfo->contentTypeId );

        $fieldDefinition = $contentType->getFieldDefinition( $fieldDefIdentifier );

        return $this->parseValue( $fieldDefinition->fieldTypeIdentifier, $value );
    }

    /**
     * Parses the givend $value using the FieldType identified by
     * $fieldTypeIdentifier
     *
     * @param mixed $fieldTypeIdentifier
     * @param mixed $value
     * @return void
     */
    public function parseValue( $fieldTypeIdentifier, $value )
    {
        $fieldType = $this->fieldTypeService->getFieldType( $fieldTypeIdentifier );
        return $fieldType->fromHash( $value );
    }
}
