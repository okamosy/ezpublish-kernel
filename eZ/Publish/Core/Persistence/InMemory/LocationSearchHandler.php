<?php
/**
 * File containing the LocationSearchHandler implementation
 *
 * @copyright Copyright (C) 1999-2014 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\Core\Persistence\InMemory;

use eZ\Publish\SPI\Persistence\Content\Location\Search\Handler as LocationSearchHandlerInterface;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use eZ\Publish\API\Repository\Values\Content\Search\SearchHit;

/**
 * @see eZ\Publish\SPI\Persistence\Content\Location\Search\Handler
 */
class LocationSearchHandler implements LocationSearchHandlerInterface
{
    /**
     * @var \eZ\Publish\Core\Persistence\InMemory\Handler
     */
    protected $handler;

    /**
     * @var \eZ\Publish\Core\Persistence\InMemory\CriterionHandler[]
     */
    private $criterionHandlers;

    /**
     * Setups current handler instance with reference to Handler object that created it.
     *
     * @param \eZ\Publish\Core\Persistence\InMemory\Handler $handler
     * @param \eZ\Publish\Core\Persistence\InMemory\Backend $backend The storage engine backend
     */
    public function __construct( Handler $handler, Backend $backend )
    {
        $this->handler = $handler;
        $this->backend = $backend;
        $this->criterionHandlers = array(
            new CriterionHandler\LocationId( $this, $backend ),
            new CriterionHandler\ParentLocationId( $this, $backend ),
            new CriterionHandler\LocationRemoteId( $this, $backend ),
            new CriterionHandler\ContentId( $this, $backend ),
            new CriterionHandler\SectionId( $this, $backend ),
            new CriterionHandler\RemoteId( $this, $backend ),
            new CriterionHandler\ContentTypeId( $this, $backend ),
            new CriterionHandler\ContentTypeIdentifier( $this, $backend ),
            new CriterionHandler\ContentTypeGroupId( $this, $backend ),
            new CriterionHandler\Subtree( $this, $backend ),
            new CriterionHandler\LogicalAnd( $this, $backend ),
            new CriterionHandler\LogicalOr( $this, $backend ),
            new CriterionHandler\LogicalNot( $this, $backend ),
        );
    }

    /**
     * Finds locations for the given $query.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\LocationQuery $query
     *
     * @return array
     */
    public function findLocations( LocationQuery $query )
    {
        $start = microtime( true );
        $match = $excludeMatch = array();
        $this->convertCriteria( $query->filter, $match, $excludeMatch );

        $result = new SearchResult();
        if ( $match === false )
        {
            return $result;
        }

        $allLocations = $this->backend->find(
            'Content\\Location',
            $match,
            $excludeMatch
        );
        $result->time = microtime( true ) - $start;
        $result->totalCount = count( $allLocations );

        $pageLocations = array_slice(
            $allLocations,
            $query->offset,
            $query->limit
        );

        foreach ( $pageLocations as $location )
        {
            $result->searchHits[] = new SearchHit( array( "valueObject" => $location ) );
        }

        return $result;
    }

    /**
     * Counts all locations given some $criterion.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Query\Criterion $criterion
     *
     * @return int
     */
    public function getLocationCount( Criterion $criterion )
    {
        $match = $excludeMatch = array();
        $this->convertCriteria( $criterion, $match, $excludeMatch );

        if ( $match === false )
        {
            return 0;
        }

        return $this->backend->count(
            'Content\\Location',
            $match,
            $excludeMatch
        );
    }

    public function convertCriteria( Criterion $criterion, array &$match, array &$excludeMatch )
    {
        foreach ( $this->criterionHandlers as $criterionHandler )
        {
            if ( $criterionHandler->accept( $criterion ) )
            {
                $criterionHandler->handle( $criterion, $match, $excludeMatch );
            }
        }
    }

}
