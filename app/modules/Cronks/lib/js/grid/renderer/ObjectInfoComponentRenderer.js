// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2012 Icinga Developer Team.
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

Ext.ns('Cronk.grid');

(function() {

    Cronk.grid.ObjectInfoComponentRenderer = new (Ext.extend(Ext.Window, {
        width: '80%',
        height: 400,
        title: _('Object information'),
        closeAction: 'hide',
        layout: 'fit',
        modal: true,
        
        constructor : function() {
            this.addEvents({
                "showobjectinfo": true
            });
            
            Ext.Window.prototype.constructor.call(this, {});
        },
        
        initComponent : function() {
            this.bbar = ['->', {
                text: _('Close'),
                iconCls: 'icinga-action-icon-cancel',
                handler: (function(b, e) {
                    this.hide();
                }).createDelegate(this)
            }];
            
            Ext.Window.prototype.initComponent.call(this);
            
            this.tabs = new Ext.TabPanel();
            
            this.tabItems = {
                host: {},
                service: {}
            };
            
            Ext.iterate(this.tabItems, function(k, v) {
                v.information = new Icinga.Cronks.Tackle.Information.Head({
                    type: k,
                    connection: this.connection
                });
                
                // We do not want to call this explicit
                v.information.getStore().on('beforeload', function() {
                    this.show();
                    this.getEl().mask(_('Loading . . .'));
                }, this);
                
                v.information.getStore().on('load', function() {
                    this.getEl().unmask();
                }, this);
                
                v.relation = new Icinga.Cronks.Tackle.Relation.Head({
                    type: k
                });
                v.externalRefs = new Icinga.Cronks.Tackle.ExternalReferences.Head({
                    type: k,
                    connection: this.connection
                });
                Ext.iterate(v, function(k2, v2) {
                    this.tabs.add(v2)
                }, this);
            }, this);
            
            this.add(this.tabs);
            
            this.doLayout();
            
            this.on('showobjectinfo', this.onShowObjectInfo, this);
            
            this.on('beforeshow', function(me) {
                if (me.type) {
                    me.prepareView(me.type);
                }
            });
        },
        
        prepareView : function(type) {
            var hide = (type==="host") ? "service" : "host";
            var show = (hide==="host") ? "service" : "host";
            
            Ext.iterate(this.tabItems[hide], function(k, object) {
                this.tabs.hideTabStripItem(object);
            }, this);
            
            Ext.iterate(this.tabItems[show], function(k, object) {
                this.tabs.unhideTabStripItem(object);
            }, this);
        },
        
       // Private
        onShowObjectInfo : function(type, oid,connection) {
            this.type = type;
            
            Ext.iterate(this.tabItems[type], function(k, object) {
                if (!Ext.isEmpty(object.loadDataForObjectId)) {
                  object.loadDataForObjectId(oid,connection);
                } else {
                    throw("WHOO, loadDataForObjectId is not implemented!");
                }
            }, this);
            
            this.tabs.setActiveTab(this.tabItems[type].information);
            
        },
        
        showObjectInfo : function(type, oid,connection) {
            this.fireEvent('showobjectinfo', type, oid,connection);
        },
        
        infoColumn : function(cfg) {
            if (Ext.isEmpty(cfg.object_id)) {
                throw('object_id must be configured');
            }
            
            if (Ext.isEmpty(cfg.type)) {
                throw('object_id must be configured');
            }
            
            return function(grid, rowIndex, colIndex, e) {
                var fieldName = grid.getColumnModel().getDataIndex(colIndex);
                if (fieldName == cfg.field) {
                    var data = grid.getStore().getAt(rowIndex).data;
                    
                    if (Ext.isEmpty(data[cfg.object_id])) {
                        throw("Could not find object_id in field " + cfg.object_id);
                    }
                    
                    var id = data[cfg.object_id];
                    var type = cfg.type;
                    
                    Cronk.grid.ObjectInfoComponentRenderer.showObjectInfo(type, id,grid.selectedConnection);
                }
            };
        }
    }));

})();