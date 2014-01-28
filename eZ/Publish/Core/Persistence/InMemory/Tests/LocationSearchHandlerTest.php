<?php
/**
 * File contains: eZ\Publish\Core\Persistence\InMemory\Tests\LocationSearchHandlerTest class
 *
 * @copyright Copyright (C) 1999-2014 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\Core\Persistence\InMemory\Tests;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;

/**
 * Test case for Location Search Handler using in memory storage.
 */
class LocationSearchHandlerTest extends LocationHandlerTest
{
    /**
     * Test for findLocations() method.
     *
     * @dataProvider providerForTestFindLocations
     * @covers \eZ\Publish\Core\Persistence\InMemory\LocationSearchHandler::findLocations
     * @group locationSearchHandler
     */
    public function testFindLocations( LocationQuery $query, $results )
    {
        $result = $this->persistenceHandler->locationSearchHandler()->findLocations( $query );
        $locations = array();
        foreach ( $result->searchHits as $searchHit )
        {
            $locations[] = $searchHit->valueObject;
        }
        usort(
            $locations,
            function ( $a, $b )
            {
                if ( $a->id == $b->id )
                    return 0;

                return ( $a->id < $b->id ) ? -1 : 1;
            }
        );
        $this->assertEquals( count( $results ), count( $locations ) );
        foreach ( $results as $n => $result )
        {
            foreach ( $result as $key => $value )
            {
                $this->assertEquals( $value, $locations[$n]->$key );
            }
        }
    }

    public function providerForTestFindLocations()
    {
        return array(
            array(
                new LocationQuery(
                    array(
                        "filter" => new Criterion\Location\ParentLocationId( 1 )
                    )
                ),
                array(
                    array( "id" => 2, "parentId" => 1 ),
                    array( "id" => 5, "parentId" => 1 ),
                    array( "id" => 43, "parentId" => 1 ),
                )
            ),
            array(
                new LocationQuery(
                    array(
                        "filter" => new Criterion\ContentId( 54 )
                    )
                ),
                array( array( "id" => 56, "contentId" => 54 ) )
            ),
            array(
                new LocationQuery(
                    array(
                        "filter" => new Criterion\Location\RemoteId( "locationRemote1" )
                    )
                ),
                array( array( "id" => 55, "remoteId" => "locationRemote1" ) )
            ),
            array(
                new LocationQuery(
                    array(
                        "filter" => new Criterion\SectionId( 3 )
                    )
                ),
                array(
                    array( "id" => 43 ),
                    array( "id" => 53 ),
                )
            ),
            array(
                new LocationQuery(
                    array(
                        "filter" => new Criterion\RemoteId( "contentRemote1" )
                    )
                ),
                array(
                    array( "id" => 55, "remoteId" => "locationRemote1" ),
                )
            ),
            array(
                new LocationQuery(
                    array(
                        "filter" => new Criterion\ContentTypeId( 3 )
                    )
                ),
                array(
                    array( "id" => 5 ),
                    array( "id" => 12 ),
                    array( "id" => 13 ),
                    array( "id" => 44 ),
                )
            ),
            array(
                new LocationQuery(
                    array(
                        "filter" => new Criterion\ContentTypeIdentifier( "user_group" )
                    )
                ),
                array(
                    array( "id" => 5 ),
                    array( "id" => 12 ),
                    array( "id" => 13 ),
                    array( "id" => 44 ),
                )
            ),
            array(
                new LocationQuery(
                    array(
                        "filter" => new Criterion\ContentTypeGroupId( 2 )
                    )
                ),
                array(
                    array( "id" => 5 ),
                    array( "id" => 12 ),
                    array( "id" => 13 ),
                    array( "id" => 44 ),
                )
            ),
            array(
                new LocationQuery(
                    array(
                        "filter" => new Criterion\Location\ParentLocationId( 54 )
                    )
                ),
                array( array( "id" => 55, "parentId" => 54 ) )
            ),
            array(
                new LocationQuery(
                    array(
                        "filter" => new Criterion\Location\Id( 55 )
                    )
                ),
                array( array( "id" => 55 ) )
            ),
            array(
                new LocationQuery(
                    array(
                        "filter" => new Criterion\LogicalAnd(
                            array(
                                new Criterion\Location\RemoteId( "locationRemote1" ),
                                new Criterion\Location\ParentLocationId( 54 )
                            )
                        )
                    )
                ),
                array( array( "id" => 55, "parentId" => 54, "remoteId" => "locationRemote1" ) )
            ),
            array(
                new LocationQuery(
                    array(
                        "filter" => new Criterion\LogicalAnd(
                            array(
                                new Criterion\LogicalAnd(
                                    array(
                                        new Criterion\Location\RemoteId( "locationRemote1" ),
                                        new Criterion\Location\ParentLocationId( 54 )
                                    )
                                ),
                                new Criterion\Location\ParentLocationId( 54 )
                            )
                        )
                    )
                ),
                array( array( "id" => 55, "parentId" => 54, "remoteId" => "locationRemote1" ) )
            ),
            array(
                new LocationQuery(
                    array(
                        "filter" => new Criterion\LogicalAnd(
                            array(
                                new Criterion\Location\Id( 55 ),
                                new Criterion\LogicalAnd(
                                    array(
                                        new Criterion\Location\RemoteId( "locationRemote1" ),
                                        new Criterion\Location\ParentLocationId( 54 )
                                    )
                                )
                            )
                        )
                    )
                ),
                array( array( "id" => 55, "parentId" => 54, "remoteId" => "locationRemote1" ) )
            ),
            array(
                new LocationQuery(
                    array(
                        "filter" => new Criterion\LogicalAnd(
                            array(
                                new Criterion\Location\Id( 54 ),
                                new Criterion\LogicalAnd(
                                    array(
                                        new Criterion\Location\RemoteId( "locationRemote1" ),
                                        new Criterion\Location\ParentLocationId( 54 )
                                    )
                                )
                            )
                        )
                    )
                ),
                array()
            ),
            array(
                new LocationQuery(
                    array(
                        "filter" => new Criterion\LogicalAnd(
                            array(
                                new Criterion\Location\RemoteId( "locationRemote0" ),
                                new Criterion\Location\ParentLocationId( 54 )
                            )
                        )
                    )
                ),
                array()
            ),
            array(
                new LocationQuery(
                    array(
                        "filter" => new Criterion\LogicalAnd(
                            array(
                                new Criterion\Location\ParentLocationId( 1 ),
                                new Criterion\Location\Id( 43 ),
                            )
                        )
                    )
                ),
                array(
                    array( "id" => 43, "parentId" => 1 ),
                )
            ),
            array(
                new LocationQuery(
                    array(
                        "filter" => new Criterion\LogicalAnd(
                            array(
                                new Criterion\Location\ParentLocationId( 1 ),
                                new Criterion\Location\ParentLocationId( 1 ),
                                new Criterion\Location\ParentLocationId( 1 ),
                            )
                        )
                    )
                ),
                array(
                    array( "id" => 2, "parentId" => 1 ),
                    array( "id" => 5, "parentId" => 1 ),
                    array( "id" => 43, "parentId" => 1 ),
                )
            ),
            array(
                new LocationQuery(
                    array(
                        "filter" => new Criterion\LogicalOr(
                            array(
                                new Criterion\Location\RemoteId( "locationRemote1" ),
                                new Criterion\Location\ParentLocationId( 54 ),
                                new Criterion\Location\RemoteId( "ARemoteIdThatDoesNotExist" ),
                            )
                        )
                    )
                ),
                array( array( "id" => 55, "parentId" => 54, "remoteId" => "locationRemote1" ) )
            ),
            array(
                new LocationQuery(
                    array(
                        "filter" => new Criterion\LogicalOr(
                            array(
                                new Criterion\Location\RemoteId( "locationRemote0" ),
                                new Criterion\LogicalOr(
                                    array(
                                        new Criterion\Location\RemoteId( "locationRemote1" ),
                                        new Criterion\Location\ParentLocationId( 54 ),
                                    )
                                )
                            )
                        )
                    )
                ),
                array(
                    array( "id" => 54, "remoteId" => "locationRemote0" ),
                    array( "id" => 55, "parentId" => 54, "remoteId" => "locationRemote1" )
                )
            ),
            array(
                new LocationQuery(
                    array(
                        "filter" => new Criterion\LogicalOr(
                            array(
                                new Criterion\Location\RemoteId( "locationRemote1" ),
                                new Criterion\Location\RemoteId( "ARemoteIdThatDoesNotExist" ),
                            )
                        )
                    )
                ),
                array(
                    array( "id" => 55, "remoteId" => "locationRemote1" ),
                )
            ),
            array(
                new LocationQuery(
                    array(
                        "filter" => new Criterion\Location\Subtree(
                            "/1/2/"
                        )
                    )
                ),
                array(
                    array( "id" => 54 ),
                    array( "id" => 55 ),
                    array( "id" => 56 ),
                    array( "id" => 57 ),
                    array( "id" => 58 ),
                )
            ),
            array(
                new LocationQuery(
                    array(
                        "filter" => new Criterion\LogicalAnd(
                            array(
                                new Criterion\Location\Subtree(
                                    "/1/2/"
                                ),
                                new Criterion\LogicalNot(
                                    new Criterion\Location\RemoteId( "locationRemote1" )
                                ),
                            )
                        )
                    )
                ),
                array(
                    array( "id" => 54 ),
                    array( "id" => 56 ),
                    array( "id" => 57 ),
                    array( "id" => 58 ),
                )
            ),
            array(
                new LocationQuery(
                    array(
                        "filter" => new Criterion\LogicalAnd(
                            array(
                                new Criterion\LogicalNot(
                                    new Criterion\Location\Subtree(
                                        "/1/2/"
                                    )
                                ),
                                new Criterion\LogicalNot(
                                    new Criterion\Location\Subtree(
                                        "/1/5/"
                                    )
                                ),
                            )
                        )
                    )
                ),
                array(
                    array( "id" => 1, "parentId" => 0 ),
                    array( "id" => 2, "parentId" => 1 ),
                    array( "id" => 5, "parentId" => 1 ),
                    array( "id" => 43, "parentId" => 1 ),
                    array( "id" => 53, "parentId" => 43 ),
                )
            ),
        );
    }
}
