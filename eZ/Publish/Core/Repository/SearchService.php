<?php
/**
 * File containing the eZ\Publish\Core\Repository\SearchService class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\Core\Repository;

use eZ\Publish\API\Repository\SearchService as SearchServiceInterface,
    eZ\Publish\API\Repository\Values\Content\Query\Criterion,
    eZ\Publish\API\Repository\Values\Content\Query,
    eZ\Publish\API\Repository\Repository as RepositoryInterface,
    eZ\Publish\SPI\Persistence\Handler,
    eZ\Publish\Core\Repository\Values\Content\ContentInfo,
    eZ\Publish\SPI\Persistence\Content\ContentInfo as SPIContentInfo;

/**
 * Search service
 *
 * @package eZ\Publish\Core\Repository
 */
class SearchService implements SearchServiceInterface
{
    /**
     * @var \eZ\Publish\API\Repository\Repository
     */
    protected $repository;

    /**
     * @var \eZ\Publish\SPI\Persistence\Handler
     */
    protected $persistenceHandler;

    /**
     * @var array
     */
    protected $settings;

    /**
     * Setups service with reference to repository object that created it & corresponding handler
     *
     * @param \eZ\Publish\API\Repository\Repository  $repository
     * @param \eZ\Publish\SPI\Persistence\Handler $handler
     * @param array $settings
     */
    public function __construct( RepositoryInterface $repository, Handler $handler, array $settings = array() )
    {
        $this->repository = $repository;
        $this->persistenceHandler = $handler;
        $this->settings = $settings;
    }

     /**
     * finds content objects for the given query.
     *
     * @TODO define structs for the field filters
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Query $query
     * @param array  $fieldFilters - a map of filters for the returned fields.
     *        Currently supported: <code>array("languages" => array(<language1>,..))</code>.
     * @param boolean $filterOnUserPermissions if true only the objects which is the user allowed to read are returned.
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Search\SearchResult
     */
    public function findContent( Query $query, array $fieldFilters = array(), $filterOnUserPermissions = true )
    {
        // @TODO: Apply permission checks
        $result = $this->persistenceHandler->searchHandler()->findContent( $query, $fieldFilters );
        foreach ( $result->searchHits as $hit )
        {
            $hit->valueObject = $this->buildContentInfoDomainObject( $hit->valueObject->contentInfo );
        }

        return $result;
    }

    /**
     * Builds a ContentInfo domain object from value object returned from persistence
     *
     * @TODO: This is a copy from the content service. All related methods
     * should be extracted in its own class. We cannot agregate the content
     * service here, since the content service already requires the search
     * service.
     *
     * @param \eZ\Publish\SPI\Persistence\Content\ContentInfo $spiContentInfo
     *
     * @return \eZ\Publish\Core\Repository\Values\Content\ContentInfo
     */
    protected function buildContentInfoDomainObject( SPIContentInfo $spiContentInfo )
    {
        $modificationDate = new \DateTime( "@{$spiContentInfo->modificationDate}" );
        $publishedDate = new \DateTime( "@{$spiContentInfo->publicationDate}" );

        // @todo: $mainLocationId should have been removed through SPI refactoring?
        $spiContent = $this->persistenceHandler->contentHandler()->load(
            $spiContentInfo->id,
            $spiContentInfo->currentVersionNo
        );
        $mainLocationId = null;
        foreach ( $spiContent->locations as $spiLocation )
        {
            if ( $spiLocation->mainLocationId === $spiLocation->id )
            {
                $mainLocationId = $spiLocation->mainLocationId;
                break;
            }
        }

        return new ContentInfo(
            array(
                "id" => $spiContentInfo->id,
                "name" => $spiContentInfo->name,
                "sectionId" => $spiContentInfo->sectionId,
                "currentVersionNo" => $spiContentInfo->currentVersionNo,
                "published" => $spiContentInfo->isPublished,
                "ownerId" => $spiContentInfo->ownerId,
                "modificationDate" => $modificationDate,
                "publishedDate" => $publishedDate,
                "alwaysAvailable" => $spiContentInfo->isAlwaysAvailable,
                "remoteId" => $spiContentInfo->remoteId,
                "mainLanguageCode" => $spiContentInfo->mainLanguageCode,
                "mainLocationId" => $mainLocationId,
                "contentType" => $this->repository->getContentTypeService()->loadContentType(
                    $spiContentInfo->contentTypeId
                )
            )
        );
    }

    /**
     * Performs a query for a single content object
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException if the object was not found by the query or due to permissions
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException if there is more than than one result matching the criterions
     *
     * @TODO define structs for the field filters
     * @param \eZ\Publish\API\Repository\Values\Content\Query\Criterion $criterion
     * @param array  $fieldFilters - a map of filters for the returned fields.
     *        Currently supported: <code>array("languages" => array(<language1>,..))</code>.
     * @param boolean $filterOnUserPermissions if true only the objects which is the user allowed to read are returned.
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Content
     */
    public function findSingle( Criterion $criterion, array $fieldFilters = array(), $filterOnUserPermissions = true )
    {

    }

    /**
     * Suggests a list of values for the given prefix
     *
     * @param string $prefix
     * @param string[] $fieldpath
     * @param int $limit
     * @param \eZ\Publish\API\Repository\Values\Content\Query\Criterion $filter
     */
    public function suggest( $prefix, $fieldPaths = array(), $limit = 10, Criterion $filter = null )
    {

    }
}