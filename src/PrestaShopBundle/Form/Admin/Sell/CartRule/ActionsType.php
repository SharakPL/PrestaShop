<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\CartRule;

use PrestaShopBundle\Form\Admin\Sell\Product\SearchedProductType;
use PrestaShopBundle\Form\Admin\Type\EntitySearchInputType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ActionsType extends TranslatorAwareType
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var string
     */
    private $employeeIsoCode;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        RouterInterface $router,
        string $employeeIsoCode
    ) {
        parent::__construct($translator, $locales);
        $this->router = $router;
        $this->employeeIsoCode = $employeeIsoCode;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('free_shipping', SwitchType::class, [
                'required' => false,
            ])
            ->add('discount', DiscountType::class, [
                'required' => false,
                'row_attr' => [
                    'class' => 'discount-container',
                ],
                'disabling_switch' => true,
                'disabled_value' => static function (?array $data) {
                    return empty($data['reduction']['value']);
                },
            ])
            ->add('gift_product', EntitySearchInputType::class, [
                'required' => false,
                'label' => $this->trans('Send a free gift', 'Admin.Catalog.Feature'),
                'remote_url' => $this->router->generate('admin_products_v2_search_combinations', [
                    'languageCode' => $this->employeeIsoCode,
                    'query' => '__QUERY__',
                ]),
                'entry_type' => SearchedProductType::class,
                'attr' => [
                    'data-reference-label' => $this->trans('Ref: %s', 'Admin.Catalog.Feature'),
                ],
                'min_length' => 3,
                'limit' => 1,
                'identifier_field' => 'unique_identifier',
                'placeholder' => $this->trans('Search product', 'Admin.Catalog.Help'),
            ])
        ;
    }
}
