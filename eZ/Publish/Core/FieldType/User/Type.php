<?php
/**
 * File containing the User class
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\Core\FieldType\User;
use eZ\Publish\Core\FieldType\FieldType,
    eZ\Publish\SPI\Persistence\Content\FieldValue,
    eZ\Publish\Core\Base\Exceptions\InvalidArgumentType;

/**
 * The User field type.
 *
 * This field type represents a simple string.
 */
class Type extends FieldType
{
    /**
     * Return the field type identifier for this field type
     *
     * @return string
     */
    public function getFieldTypeIdentifier()
    {
        return "ezuser";
    }

    /**
     * Returns the name of the given field value.
     *
     * It will be used to generate content name and url alias if current field is designated
     * to be used in the content name/urlAlias pattern.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function getName( $value )
    {
        $value = $this->acceptValue( $value );

        return (string) $value->login;
    }

    /**
     * Returns the fallback default value of field type when no such default
     * value is provided in the field definition in content types.
     *
     * @return \eZ\Publish\Core\FieldType\User\Value
     * @TODO: Implement.
     */
    public function getEmptyValue()
    {
        return null;
    }

    /**
     * Returns true if value is considered empty else returns false
     *
     * @param mixed $value
     *
     * @return boolean
     */
    public function isEmpty( $value )
    {
        if ( !isset( $value ) )
        {
            return true;
        }

        $value = $this->acceptValue( $value );

        return !isset( $value->login ) || !$value->isEnabled;
    }

    /**
     * Checks the type and structure of the $Value.
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException if the parameter is not of the supported value sub type
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException if the value does not match the expected structure
     *
     * @param \eZ\Publish\Core\FieldType\User\Value $inputValue
     *
     * @return \eZ\Publish\Core\FieldType\User\Value
     * @TODO: Implement.
     */
    public function acceptValue( $inputValue )
    {
        if ( is_array( $inputValue ) )
        {
            $inputValue = new Value( $inputValue );
        }

        if ( !( $inputValue instanceof Value ) )
        {
            throw new InvalidArgumentType(
                '$inputValue',
                'eZ\\Publish\\Core\\FieldType\\User\\Value',
                $inputValue
            );
        }

        return $inputValue;
    }

    /**
     * Returns information for FieldValue->$sortKey relevant to the field type.
     *
     * @TODO: Implement.
     */
    protected function getSortInfo( $value )
    {
        return false;
    }

    /**
     * Converts an $hash to the Value defined by the field type
     *
     * @param mixed $hash
     *
     * @return \eZ\Publish\Core\FieldType\User\Value $value
     * @TODO: Implement.
     */
    public function fromHash( $hash )
    {
        $values = array_merge(
            array(
                'login'      => null,
                'accountKey' => null,
                'lastVisit'  => null,
                'loginCount' => null,
            ),
            $hash
        );

        return new Value( array(
            'login'      => $values['login'],
            'accountKey' => $values['accountKey'],
            'lastVisit'  => $values['lastVisit'],
            'loginCount' => $values['loginCount'],
        ) );
    }

    /**
     * Converts a $Value to a hash
     *
     * @param \eZ\Publish\Core\FieldType\User\Value $value
     *
     * @return mixed
     * @TODO: Implement.
     */
    public function toHash( $value )
    {
        $values = array_merge(
            array(
                'login'      => null,
                'accountKey' => null,
                'lastVisit'  => null,
                'loginCount' => null,
            ),
            (array) $value
        );

        return array(
            'login'      => $values['login'],
            'accountKey' => $values['accountKey'],
            'lastVisit'  => $values['lastVisit'],
            'loginCount' => $values['loginCount'],
        );
    }

     /**
     * Converts a $value to a persistence value.
     *
     * In this method the field type puts the data which is stored in the field of content in the repository
     * into the property FieldValue::data. The format of $data is a primitive, an array (map) or an object, which
     * is then canonically converted to e.g. json/xml structures by future storage engines without
     * further conversions. For mapping the $data to the legacy database an appropriate Converter
     * (implementing eZ\Publish\Core\Persistence\Legacy\FieldValue\Converter) has implemented for the field
     * type. Note: $data should only hold data which is actually stored in the field. It must not
     * hold data which is stored externally.
     *
     * The $externalData property in the FieldValue is used for storing data externally by the
     * FieldStorage interface method storeFieldData.
     *
     * The FieldValuer::sortKey is build by the field type for using by sort operations.
     *
     * @see \eZ\Publish\SPI\Persistence\Content\FieldValue
     *
     * @param mixed $value The value of the field type
     *
     * @return \eZ\Publish\SPI\Persistence\Content\FieldValue the value processed by the storage engine
     */
    public function toPersistenceValue( $value )
    {
        return new FieldValue(
            array(
                "data" => array(),
                "externalData" => null,
                "sortKey" => null,
            )
        );
    }

    /**
     * Converts a persistence $fieldValue to a Value
     *
     * This method builds a field type value from the $data and $externalData properties.
     *
     * @param \eZ\Publish\SPI\Persistence\Content\FieldValue $fieldValue
     *
     * @return mixed
     * @TODO: Implement.
     */
    public function fromPersistenceValue( FieldValue $fieldValue )
    {
        // return new Value( $fieldValue->externalData );
        return new Value();
    }
}
