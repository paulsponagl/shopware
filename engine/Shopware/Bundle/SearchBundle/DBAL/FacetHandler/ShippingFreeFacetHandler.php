<?php
/**
 * Shopware 4
 * Copyright © shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

namespace Shopware\Bundle\SearchBundle\DBAL\FacetHandler;

use Shopware\Bundle\SearchBundle\Criteria;
use Shopware\Bundle\SearchBundle\DBAL\QueryBuilder;
use Shopware\Bundle\SearchBundle\DBAL\FacetHandlerInterface;
use Shopware\Bundle\SearchBundle\Facet;
use Shopware\Bundle\SearchBundle\FacetInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContextInterface;

/**
 * @category  Shopware
 * @package   Shopware\Bundle\SearchBundle\DBAL\FacetHandler
 * @copyright Copyright (c) shopware AG (http://www.shopware.de)
 */
class ShippingFreeFacetHandler implements FacetHandlerInterface
{
    /**
     * Generates the facet data for the passed query, criteria and context object.
     *
     * @param FacetInterface|Facet\ShippingFreeFacet $facet
     * @param QueryBuilder $query
     * @param Criteria $criteria
     * @param ShopContextInterface $context
     * @return Facet\ShippingFreeFacet
     */
    public function generateFacet(
        FacetInterface $facet,
        QueryBuilder $query,
        Criteria $criteria,
        ShopContextInterface $context
    ) {
        $query->resetQueryPart('orderBy');
        $query->resetQueryPart('groupBy');

        $query->select(array(
            'COUNT(DISTINCT product.id) as total'
        ));

        $query->andWhere('variant.shippingfree = 1');

        /**@var $statement \Doctrine\DBAL\Driver\ResultStatement */
        $statement = $query->execute();

        $total = $statement->fetch(\PDO::FETCH_COLUMN);

        $facet->setTotal($total);

        if ($criteria->getCondition('shipping_free')) {
            $facet->setIsFiltered(true);
        }

        return $facet;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsFacet(FacetInterface $facet)
    {
        return ($facet instanceof Facet\ShippingFreeFacet);
    }

}