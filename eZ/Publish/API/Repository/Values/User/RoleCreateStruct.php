<?php
namespace eZ\Publish\API\Repository\Values\User;
use eZ\Publish\API\Repository\Values\ValueObject;
use eZ\Publish\API\Repository\Values\User\PolicyCreateStruct;

/**
 * This class is used to create a new role
 */
abstract class RoleCreateStruct extends ValueObject
{
    /**
     * Readable string identifier of a role
     *
     * @var string
     */
    public $identifier;

    /**
     * the main language code
     *
     * @since 5.0
     *
     * @var string
     */
    public $mainLanguageCode;
    
   /**
     * An array of names with languageCode keys
     * 
     * @since 5.0
     *
     * @var array an array of string
     */
    public $names;

    /**
     * An array of descriptions with languageCode keys
     * 
     * @since 5.0
     *
     * @var array an array of string
     */
    public $descriptions;

    /**
     * Returns policies associated with the role
     *
     * @return \eZ\Publish\API\Repository\Values\User\PolicyCreateStruct[]
     */
    abstract public function getPolicies();

    /**
     * Adds a policy to this role
     *
     * @param \eZ\Publish\API\Repository\Values\User\PolicyCreateStruct $policyCreateStruct
     */
    abstract public function addPolicy( PolicyCreateStruct $policyCreateStruct );

}