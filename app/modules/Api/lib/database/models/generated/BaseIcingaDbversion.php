<?php

/**
 * BaseIcingaDbversion
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @property string $name
 * @property string $version
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseIcingaDbversion extends Doctrine_Record {
    public function setTableDefinition() {
        $prefix = Doctrine_Manager::getInstance()->getConnection(IcingaDoctrineDatabase::CONNECTION_ICINGA)->getPrefix();
        $this->setTableName($prefix.'dbversion');
        $this->hasColumn('name', 'string', 10, array(
                             'type' => 'string',
                             'length' => 10,
                             'fixed' => false,
                             'unsigned' => false,
                             'primary' => false,
                             'default' => '',
                             'notnull' => true,
                             'autoincrement' => false,
                         ));
        $this->hasColumn('version', 'string', 10, array(
                             'type' => 'string',
                             'length' => 10,
                             'fixed' => false,
                             'unsigned' => false,
                             'primary' => false,
                             'default' => '',
                             'notnull' => true,
                             'autoincrement' => false,
                         ));
    }

    public function setUp() {
        parent::setUp();

    }
}