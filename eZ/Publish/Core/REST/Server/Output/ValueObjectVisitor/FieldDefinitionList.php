<?php
/**
 * File containing the FieldDefinitionList visitor class
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\Core\REST\Server\Output\ValueObjectVisitor;

use eZ\Publish\Core\REST\Common\Output\ValueObjectVisitor;
use eZ\Publish\Core\REST\Common\Output\Generator;
use eZ\Publish\Core\REST\Common\Output\Visitor;

use eZ\Publish\Core\REST\Server\Values\RestFieldDefinition;

use eZ\Publish\API\Repository\Values;

/**
 * FieldDefinitionList value object visitor
 */
class FieldDefinitionList extends ContentTypeBase
{
    /**
     * Visit struct returned by controllers
     *
     * @param \eZ\Publish\Core\REST\Common\Output\Visitor $visitor
     * @param \eZ\Publish\Core\REST\Common\Output\Generator $generator
     * @param mixed $data
     */
    public function visit( Visitor $visitor, Generator $generator, $data )
    {
        $fieldDefinitionList = $data;
        $contentType = $fieldDefinitionList->contentType;

        $urlTypeSuffix = $this->getUrlTypeSuffix( $contentType->status );

        $generator->startObjectElement( 'FieldDefinitions', 'FieldDefinitionList' );
        $visitor->setHeader( 'Content-Type', $generator->getMediaType( 'FieldDefinitionList' ) );

        $generator->startAttribute(
            'href',
            $this->urlHandler->generate(
                'typeFieldDefinitions'  . $urlTypeSuffix,
                array(
                    'type' => $contentType->id
                )
            )
        );
        $generator->endAttribute( 'href' );

        $generator->startList( 'FieldDefinition' );
        foreach ( $fieldDefinitionList->fieldDefinitions as $fieldDefinition )
        {
            $visitor->visitValueObject(
                new RestFieldDefinition( $contentType, $fieldDefinition )
            );
        }
        $generator->endList( 'FieldDefinition' );

        $generator->endObjectElement( 'FieldDefinitions' );
    }
}

