<?php
/**
 * File containing the XmlTextTest class
 *
 * @copyright Copyright (C) 1999-2011 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\Core\Persistence\Legacy\Tests\Content\FieldValue\Converter;
use ezp\Content\FieldType\XmlText\Value as XmlTextValue,
    ezp\Content\FieldType\FieldSettings,
    eZ\Publish\SPI\Persistence\Content\FieldValue,
    eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldValue,
    eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldDefinition,
    eZ\Publish\Core\Persistence\Legacy\Content\FieldValue\Converter\XmlText as XmlTextConverter,
    eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition as PersistenceFieldDefinition,
    PHPUnit_Framework_TestCase,
    DOMDocument;

/**
 * Test case for XmlText converter in Legacy storage
 *
 * @group fieldType
 * @group ezxmltext
 */
class XmlTextTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \eZ\Publish\Core\Persistence\Legacy\Content\FieldValue\Converter\Author
     */
    protected $converter;

    /**
     * @var \ezp\Content\FieldType\Author\Author[]
     */
    private $authors;

    protected function setUp()
    {
        self::markTestIncomplete();
        parent::setUp();
        $this->converter = new XmlTextConverter;
        $this->authors = array(
            new Author( array( 'name' => 'Boba Fett', 'email' => 'boba.fett@bountyhunters.com' ) ),
            new Author( array( 'name' => 'Darth Vader', 'email' => 'darth.vader@evilempire.biz' ) ),
            new Author( array( 'name' => 'Luke Skywalker', 'email' => 'luke@imtheone.net' ) )
        );
    }

    public function tearDown()
    {
        unset( $this->authors );
        parent::tearDown();
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\FieldValue\Converter\XmlText::toStorageValue
     */
    public function testToStorageValue()
    {
        $value = new FieldValue;
        $value->data = new AuthorValue( $this->authors );
        $storageFieldValue = new StorageFieldValue;

        $this->converter->toStorageValue( $value, $storageFieldValue );
        $doc = new DOMDocument( '1.0', 'utf-8' );
        self::assertTrue( $doc->loadXML( $storageFieldValue->dataText ) );

        $authorsXml = $doc->getElementsByTagName( 'author' );
        self::assertSame( count( $this->authors ) , $authorsXml->length );

        // Loop against XML nodes and compare them to the real Author objects.
        // Then remove Author from $this->authors
        // This way, we can check if all authors have been converted in XML
        foreach ( $authorsXml as $authorXml )
        {
            foreach ( $this->authors as $i => $author )
            {
                if ( $authorXml->getAttribute( 'id' ) == $author->id )
                {
                    self::assertSame( $author->name, $authorXml->getAttribute( 'name' ) );
                    self::assertSame( $author->email, $authorXml->getAttribute( 'email' ) );
                    unset( $this->authors[$i] );
                    break;
                }
            }
        }

        self::assertEmpty( $this->authors, 'All authors have not been converted as expected' );
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\FieldValue\Converter\XmlText::toFieldValue
     */
    public function testToFieldValue()
    {
        $storageFieldValue = new StorageFieldValue;
        $storageFieldValue->dataText = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<ezauthor>
    <authors>
        <author id="1" name="Boba Fett" email="boba.fett@bountyhunters.com"/>
        <author id="2" name="Darth Vader" email="darth.vader@evilempire.biz"/>
        <author id="3" name="Luke Skywalker" email="luke@imtheone.net"/>
    </authors>
</ezauthor>
EOT;
        $doc = new DOMDocument( '1.0', 'utf-8' );
        self::assertTrue( $doc->loadXML( $storageFieldValue->dataText ) );
        $authorsXml = $doc->getElementsByTagName( 'author' );
        $fieldValue = new FieldValue;

        $this->converter->toFieldValue( $storageFieldValue, $fieldValue );
        self::assertInstanceOf( 'ezp\\Content\\FieldType\\Author\\Value', $fieldValue->data );

        $authorsXml = $doc->getElementsByTagName( 'author' );
        self::assertInstanceOf( 'ezp\\Content\\FieldType\\Author\\AuthorCollection', $fieldValue->data->authors );
        self::assertSame( $authorsXml->length, count( $fieldValue->data->authors ) );

        $aAuthors = $fieldValue->data->authors->getArrayCopy();
        foreach ( $fieldValue->data->authors as $i => $author )
        {
            foreach ( $authorsXml as $authorXml )
            {
                if ( $authorXml->getAttribute( 'id' ) == $author->id )
                {
                    self::assertSame( $authorXml->getAttribute( 'name' ), $author->name );
                    self::assertSame( $authorXml->getAttribute( 'email' ), $author->email );
                    unset($aAuthors[$i]);
                    break;
                }
            }
        }
        self::assertEmpty( $aAuthors, 'All authors have not been converted as expected from storage' );
    }
}