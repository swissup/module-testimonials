<?php
namespace Swissup\Testimonials\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /**
         * Create table 'swissup_testimonials_data'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('swissup_testimonials_data'))
            ->addColumn(
                'testimonial_id',
                Table::TYPE_SMALLINT,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Testimonial ID'
            )
            ->addColumn('status', Table::TYPE_SMALLINT, null, ['nullable' => false, 'default' => '1'], 'Testimonial status')
            ->addColumn('date', Table::TYPE_DATETIME, null, ['nullable' => false], 'Testimonial creation time')
            ->addColumn('name', Table::TYPE_TEXT, 100, ['nullable' => false], 'User name')
            ->addColumn('email', Table::TYPE_TEXT, 100, ['nullable' => false], 'User email')
            ->addColumn('message', Table::TYPE_TEXT, null, ['nullable' => false], 'User message')
            ->addColumn('company', Table::TYPE_TEXT, 255, ['nullable' => true], 'User company')
            ->addColumn('website', Table::TYPE_TEXT, 255, ['nullable' => true], 'User website')
            ->addColumn('twitter', Table::TYPE_TEXT, 255, ['nullable' => true], 'User twitter')
            ->addColumn('facebook', Table::TYPE_TEXT, 255, ['nullable' => true], 'User facebook')
            ->addColumn('image', Table::TYPE_TEXT, 100, ['nullable' => true], 'User image path')
            ->addColumn('rating', Table::TYPE_SMALLINT, null, ['nullable' => true], 'User rating')
            ->addColumn('widget', Table::TYPE_SMALLINT, null, ['nullable' => true, 'default' => 1], 'Show testimonial in widget')
            ->addIndex(
                $setup->getIdxName(
                    $installer->getTable('swissup_testimonials_data'),
                    ['name', 'email', 'message'],
                    AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                ['name', 'email', 'message'],
                ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
            )
            ->setComment('Swissup Testimonials Data Table');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'swissup_testimonials_store'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('swissup_testimonials_store')
        )->addColumn(
            'testimonial_id',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'primary' => true],
            'Testimonial ID'
        )->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Store ID'
        )->addIndex(
            $installer->getIdxName('swissup_testimonials_store', ['store_id']),
            ['store_id']
        )->addForeignKey(
            $installer->getFkName('swissup_testimonials_store', 'testimonial_id', 'swissup_testimonials_data', 'testimonial_id'),
            'testimonial_id',
            $installer->getTable('swissup_testimonials_data'),
            'testimonial_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('swissup_testimonials_store', 'store_id', 'store', 'store_id'),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Swissup Testimonial To Store Linkage Table'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}