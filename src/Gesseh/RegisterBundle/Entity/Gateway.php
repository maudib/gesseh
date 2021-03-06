<?php

/**
 * This file is part of GESSEH project
 *
 * @author: Pierre-François ANGRAND <gesseh@medlibre.fr>
 * @copyright: Copyright 2016 Pierre-François Angrand
 * @license: GPLv3
 * See LICENSE file or http://www.gnu.org/licenses/gpl.html
 */

namespace Gesseh\RegisterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Payum\Core\Model\GatewayConfig as BaseGatewayConfig;

/**
 * Gateway
 *
 * @ORM\Table(name="payum_gateway")
 * @ORM\Entity
 */
class Gateway extends BaseGatewayConfig
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var integer $id
     */
     private $id;

    public function __toString()
    {
        return $this->gatewayName;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get Description
     *
     * @return string
     */
    public function getDescription()
    {
        if ($this->factoryName == "offline")
            return "Chèques, virement ou espèces";
        elseif ($this->factoryName == "paypal_express_checkout")
            return "Paypal";
        else
            return false;
    }
}
