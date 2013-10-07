<?php
// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2013 Icinga Developer Team.
// All rights reserved.
// 
// icinga-web is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
// 
// icinga-web is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with icinga-web.  If not, see <http://www.gnu.org/licenses/>.
// -----------------------------------------------------------------------------
// {{{ICINGA_LICENSE_CODE}}}

Doctrine_Manager::getInstance()->bindComponent('NsmRole', 'icinga_web');

/**
 * BaseNsmRole
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @property integer $role_id
 * @property string $role_name
 * @property string $role_description
 * @property integer $role_disabled
 * @property timestamp $role_created
 * @property timestamp $role_modified
 * @property integer $role_parent
 * @property Doctrine_Collection $NsmRole
 * @property Doctrine_Collection $NsmPrincipal
 * @property Doctrine_Collection $NsmUserRole
 *
 * @package    IcingaWeb
 * @subpackage AppKit
 * @author     Icinga Development Team <info@icinga.org>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseNsmRole extends Doctrine_Record {

    public function setTableDefinition() {
        $this->setTableName('nsm_role');
        $this->hasColumn('role_id', 'integer', 4, array(
                             'type' => 'integer',
                             'length' => 4,
                             'fixed' => false,
                             'unsigned' => false,
                             'primary' => true,
                             'autoincrement' => true,
                         ));
        $this->hasColumn('role_name', 'string', 40, array(
                             'type' => 'string',
                             'length' => 40,
                             'fixed' => false,
                             'unsigned' => false,
                             'primary' => false,
                             'notnull' => true,
                             'autoincrement' => false,
                         ));
        $this->hasColumn('role_description', 'string', 255, array(
                             'type' => 'string',
                             'length' => 255,
                             'fixed' => false,
                             'unsigned' => false,
                             'primary' => false,
                             'notnull' => false,
                             'autoincrement' => false,
                         ));
        $this->hasColumn('role_disabled', 'integer', 1, array(
                             'type' => 'integer',
                             'length' => 1,
                             'fixed' => false,
                             'unsigned' => false,
                             'primary' => false,
                             'default' => '0',
                             'notnull' => true,
                             'autoincrement' => false,
                         ));
        $this->hasColumn('role_created', 'timestamp', null, array(
                             'type' => 'timestamp',
                             'fixed' => false,
                             'unsigned' => false,
                             'primary' => false,
                             'notnull' => true,
                             'autoincrement' => false,
                         ));
        $this->hasColumn('role_modified', 'timestamp', null, array(
                             'type' => 'timestamp',
                             'fixed' => false,
                             'unsigned' => false,
                             'primary' => false,
                             'notnull' => true,
                             'autoincrement' => false,
                         ));
        $this->hasColumn('role_parent', 'integer', 4, array(
                             'type' => 'integer',
                             'length' => 4,
                             'fixed' => false,
                             'unsigned' => false,
                             'primary' => false,
                             'notnull' => false,
                             'autoincrement' => false,
                         ));
    }

    public function setUp() {
        parent::setUp();
        
        $this->hasOne('NsmRole as parent', array(
                          'local' => 'role_parent',
                          'foreign' => 'role_id'));
        
        $this->hasOne('NsmRole as childs', array(
                                  'local' => 'role_id',
                                  'foreign' => 'role_parent'));

        $this->hasOne('NsmPrincipal', array(
                          'local' => 'role_id',
                          'foreign' => 'principal_role_id'));

        $this->hasOne('NsmPrincipal as principal', array(
                'local' => 'role_id',
                'foreign' => 'principal_role_id'));

        $this->hasMany('NsmUserRole', array(
                           'local' => 'role_id',
                           'foreign' => 'usro_role_id'));

        $this->hasMany('NsmUserRole as userrole', array(
                'local' => 'role_id',
                'foreign' => 'usro_role_id'));
    }

    public static function getInitialData() {
        return array(
                   array('role_id'=>'1','role_name'=>'icinga_user','role_description'=>'The default representation of a ICINGA user','role_disabled'=>'0'),
                   array('role_id'=>'2','role_name'=>'appkit_user','role_description'=>'Appkit user test','role_disabled'=>'0'),
                   array('role_id'=>'3','role_name'=>'appkit_admin','role_description'=>'AppKit admin','role_disabled'=>'0','role_parent'=>'2'),
                   array('role_id'=>'4','role_name'=>'guest','role_description'=>'Unauthorized Guest','role_disabled'=>'0')
               );
    }

    public static function getPgsqlSequenceOffsets() {
        return array("nsm_role_role_id_seq" => 5);
    }
}