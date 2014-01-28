<?php
/**
 * File containing the eZ\Publish\API\Repository\Values\Content\Query\SortClause\Location\Id class.
 *
 * @copyright Copyright (C) 1999-2014 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\API\Repository\Values\Content\Query\SortClause\Location;

use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;

/**
 * Sets sort direction on Location id for a Location query
 *
 * Especially useful to get reproducible search results in tests.
 */
class Id extends SortClause
{
    /**
     * Constructs a new LocationId SortClause
     *
     * @param string $sortDirection
     */
    public function __construct( $sortDirection = Query::SORT_ASC )
    {
        parent::__construct( 'location_id', $sortDirection );
    }
}
