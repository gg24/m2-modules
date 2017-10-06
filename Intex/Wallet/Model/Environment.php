<?php
namespace Intex\Wallet\Model;


class Environment implements \Magento\Framework\Option\ArrayInterface
{
    const ENVIRONMENT_LIVE    = 'live';
    const ENVIRONMENT_TEST       = 'test';

    /**
     * Possible environment types
     * 
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::ENVIRONMENT_TEST,
                'label' => 'Test',
            ],
            [
                'value' => self::ENVIRONMENT_LIVE,
                'label' => 'Live'
            ]
        ];
    }
}
