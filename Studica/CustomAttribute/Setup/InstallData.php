<?php
/* app/code/Atwix/TestAttribute/Setup/InstallData.php */
 
namespace Studica\CustomAttribute\Setup;
 
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend;
use Magento\Customer\Model\Customer\Attribute\Source\Group;
 
/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;
 
    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }
 
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
 
        /**
         * Add attributes to the eav/attribute
         */
 
        $eavSetup->addAttribute(
			 \Magento\Catalog\Model\Category::ENTITY,
            'custom_segmentation',
            [   'group'         => 'Segmentation',
				'input'         => 'multiselect',
                'type'          => 'varchar',
				'label'         => 'Customer Segmentation',
				'backend_model' =>  'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
				'source_model'	=>	'Magento\Customer\Model\Customer\Attribute\Source\Group',
				'visible' => 1,
				'required' => 0,
				'user_defined' => 1,
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                
                
                
            ]
        );
    }
}