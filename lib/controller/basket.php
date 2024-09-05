<?php


namespace Izifir\Core\Controller;

use Bitrix\Currency\CurrencyManager;
use Bitrix\Main;
use Bitrix\Main\Context;
use Bitrix\Sale;
use Bitrix\Main\Engine\Controller;
use CIBlockElement;
use Exception;

class Basket extends Controller
{
    protected Sale\Basket\Storage $basketStorage;

    protected $fUserId;

    /**
     * @return \array[][]
     */
    public function configureActions(): array
    {
        return [
            'add' => ['prefilters' => []],
            'delete' => ['prefilters' => []],
            'updateQuantity' => ['prefilters' => []],
            'setCoupon' => ['prefilters' => []],
            'clearCoupon' => ['prefilters' => []],
        ];
    }

    /**
     * @param $productId
     * @param float $quantity
     * @param array $props
     * @param false $useMerge
     * @return array
     * @throws Main\ArgumentException
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     * @throws Main\ArgumentTypeException
     * @throws Main\NotImplementedException
     * @throws Main\NotSupportedException
     * @throws Main\ObjectNotFoundException
     * @throws Main\LoaderException
     * @throws Exception
     */
    public function addAction($productId, float $quantity = 1, array $props = ['ARTICLE'], bool $useMerge = true): array
    {
        $basket = $this->getBasketStorage()->getBasket();

        $propertyList = $this->getProperties($productId, $props);

        if ($item = $basket->getExistsItem('catalog', $productId, $propertyList)) {
            if ($useMerge)
                $item->setField('QUANTITY', $item->getQuantity() + $quantity);
            else
                $item->setField('QUANTITY', $item->getQuantity());

        } else {
            $item = $basket->createItem('catalog', $productId);
            $item->setFields([
                'QUANTITY' => $quantity,
                'CURRENCY' => CurrencyManager::getBaseCurrency(),
                'LID' => $this->getSiteId(),
                'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProvider',
            ]);

            if (!empty($propertyList)) {
                $propertyCollection = $item->getPropertyCollection();
                foreach ($propertyList as $property) {
                    $propertyItem = $propertyCollection->createItem();
                    $propertyItem->setFields($property);
                }
            }
        }

        $addResult = $basket->save();

        if ($addResult->isSuccess()) {
            $result = [
                'status' => 'success',
                'count' => $this->getBasketItemsCount()
            ];
        } else {
            $result = [
                'status' => 'error',
                'message' => implode('<br>', $addResult->getErrorMessages())
            ];
        }

        return $result;
    }

    /**
     * @param $productId
     * @param $props
     * @return array
     * @throws Main\ArgumentException
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     * @throws Main\LoaderException
     * @throws Main\NotImplementedException
     * @throws Main\ObjectNotFoundException
     */
    public function deleteAction($productId, $props): array
    {
        $basket = $this->getBasketStorage()->getBasket();
        if ($item = $basket->getExistsItem('catalog', $productId, $props)) {
            $item->delete();
        }
        $basket->save();
        return [
            'status' => 'success',
            'count' => $this->getBasketItemsCount()
        ];
    }

    /**
     * @param $coupon
     * @throws Main\ArgumentException
     * @throws Main\ArgumentNullException
     * @throws Main\InvalidOperationException
     * @throws Main\LoaderException
     * @throws Main\NotImplementedException
     * @throws Main\ObjectNotFoundException
     */
    public function setCouponAction($coupon)
    {
        Main\Loader::includeModule('sale');
        Sale\DiscountCouponsManager::clear(true);
        Sale\DiscountCouponsManager::add($coupon);
        $basket = $this->getBasketStorage()->getBasket();
        $discounts = Sale\Discount::buildFromBasket($basket, new Sale\Discount\Context\Fuser($basket->getFUserId(true)));
        $basket->refresh(Sale\Basket\RefreshFactory::create(Sale\Basket\RefreshFactory::TYPE_FULL));
        $discounts->calculate();
        $basket->save();
    }

    /**
     * @throws Main\LoaderException
     */
    public function clearCouponAction()
    {
        Main\Loader::includeModule('sale');
        Sale\DiscountCouponsManager::clear(true);
    }

    /**
     * @param $basketId
     * @param $quantity
     * @return string[]
     * @throws Main\ArgumentException
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     * @throws Main\LoaderException
     * @throws Main\NotImplementedException
     * @throws Main\ObjectNotFoundException
     */
    public function updateQuantityAction($basketId, $quantity): array
    {
        $basket = $this->getBasketStorage()->getBasket();
        if ($item = $basket->getItemById($basketId)) {
            $item->setField('QUANTITY', $quantity);
        }
        $basket->save();

        return [
            'status' => 'success'
        ];
    }

    /**
     * @return Sale\Basket\Storage|mixed
     * @throws Main\ArgumentNullException
     * @throws Main\LoaderException
     */
    protected function getBasketStorage()
    {
        Main\Loader::includeModule('sale');

        if (!isset($this->basketStorage)) {
            $this->basketStorage = Sale\Basket\Storage::getInstance($this->getFuserId(), $this->getSiteId());
        }

        return $this->basketStorage;
    }

    /**
     * @return false|int|null
     */
    protected function getFuserId()
    {
        if ($this->fUserId === null) {
            $this->fUserId = Sale\Fuser::getId();
        }

        return $this->fUserId;
    }

    /**
     * @return string
     */
    protected function getSiteId(): string
    {
        return Context::getCurrent()->getSite();
    }

    /**
     * @param $productId
     * @param $props
     * @return array
     * @throws Main\LoaderException
     */
    protected function getProperties($productId, $props): array
    {
        Main\Loader::includeModule('iblock');
        $result = [];
        if (!empty($props)) {
            $element = CIBlockElement::GetList(
                [],
                ['ID' => $productId],
                false,
                false,
                ['ID', 'IBLOCK_ID']
            )->GetNextElement();

            if ($element) {
                $propertyList = $element->GetProperties();
                foreach ($props as $prop) {
                    if (!empty($propertyList[$prop]['VALUE'])) {
                        $result[] = [
                            'CODE' => $prop,
                            'VALUE' => $propertyList[$prop]['VALUE'],
                            'SORT' => $propertyList[$prop]['SORT'],
                            'NAME' => $propertyList[$prop]['NAME']
                        ];
                    }
                }
            }
        }
        return $result;
    }

    /**
     * @throws Main\ArgumentNullException
     * @throws Main\LoaderException
     */
    protected function getBasketItemsCount(): int
    {
        return count($this->getBasketStorage()->getBasket()->getBasketItems());
    }
}
