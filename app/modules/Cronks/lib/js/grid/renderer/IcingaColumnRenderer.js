Ext.ns('Cronk.grid');

// These are the javascript methods available within
// the namespace
Cronk.grid.IcingaColumnRenderer = {
    
    subGrid : function(cfg) {
        return function(grid, rowIndex, colIndex, e) {
            var fieldName = grid.getColumnModel().getDataIndex(colIndex);
            if (fieldName == cfg.field) {
                
                var record = grid.getStore().getAt(rowIndex);
                var id = (cfg.idPrefix || 'empty') + 'subGridComponent';
                
                var cronk = {
                    parentid: id,
                    title: (cfg.titlePrefix || '') + " " + record.data[ cfg.labelField ],
                    crname: 'gridProc',
                    closable: true,
                    params: {
                        template: cfg.targetTemplate,
                        module: 'Cronks',
                        action: 'System.ViewProc'
                    }
                };
                
                var filter = {};
                
                if (cfg.filterMap) {
                    Ext.iterate(cfg.filterMap, function(k, v) {
                        filter["f[" + v + "-value]"] =  record.data[ k ];
                        filter["f[" + v + "-operator]"] = 50;
                    });
                }
                else {
                    filter["f[" + cfg.targetField + "-value]"] = record.data[ cfg.sourceField ];
                    filter["f[" + cfg.targetField + "-operator]"] = 50;
                }
                
                Cronk.util.InterGridUtil.gridFilterLink(cronk, filter);
            }
        }
    },
    
    ajaxClick : function(cfg) {
        return function(grid, rowIndex, colIndex, e) {
            var fieldName = grid.getColumnModel().getDataIndex(colIndex);
            if (fieldName == cfg.field) {

                cfg.processedFilterData = [];

                Ext.iterate(
                    cfg.filter,
                    function (key, value) {
                        this.push({
                            key: key,
                            value: grid.getStore().getAt(rowIndex).data[value]
                            });
                    },
                    cfg.processedFilterData
                    );
                
                Icinga.util.SimpleDataProvider.createToolTip({
                    title: cfg.title,
                    target: e.getTarget(),
                    srcId: cfg.src_id,
                    width: Ext.isEmpty(cfg.width) ? 400 : cfg.width,
                    autoHide: false,    
                    delay: typeof cfg.delay != "undefined" ? cfg.delay :2000,
                    filter: cfg.processedFilterData
                });

            }
        }

    },

    hyperLink : function(cfg) {

        if (!'url' in cfg) {
            throw('url XTemplate configuration needed! (parameter name="url")');
        }
        cfg.url = encodeURI(cfg.url);
        return function(grid, rowIndex, colIndex, e) {
            
            var url = Cronk.grid.ColumnRendererUtil.applyXTemplate(grid, rowIndex, cfg.url);
            
            var fieldName = grid.getColumnModel().getDataIndex(colIndex);

            if (fieldName == cfg.field) {
                var url = Cronk.grid.ColumnRendererUtil.applyXTemplate(grid, rowIndex, cfg.url)
                var windowName = fieldName;

                if (Ext.isEmpty(cfg.newWindow) || cfg.newWindow == false) {
                    windowName = '_self';
                }

                window.open(url, windowName);
            }
        }
    },

    imagePopup : function(cfg) {
        if (!'url' in cfg) {
            throw('url XTemplate configuration needed! (parameter name="url")');
        }
        return function(grid, rowIndex, colIndex, e) {
            var url = Cronk.grid.ColumnRendererUtil.applyXTemplate(grid, rowIndex, cfg.url);
            var title = Cronk.grid.ColumnRendererUtil.applyXTemplate(grid, rowIndex, cfg.title);
            
            var fieldName = grid.getColumnModel().getDataIndex(colIndex);
            if (fieldName == cfg.field) {
                
                if (Ext.isEmpty(cfg.booleanConditionField) == false) {
                    var record = grid.getStore().getAt(rowIndex);
                    if (!Cronk.grid.ColumnRendererUtil.testBooleanCondition(cfg.booleanConditionField, record)) {
                        return;
                    }
                }
                
                var dhelper_spec = {
                    tag: 'img',
                    src: encodeURI(url)
                };
                
                var qtip_spec = {
                    target: e.getTarget(),
                    bodyCfg: dhelper_spec,
                    title: title,
                    anchor: 'left'
                };
                
                Ext.iterate(['width', 'height'], function(item, index, allItems) {
                    if (!Ext.isEmpty(cfg[item])) {
                        qtip_spec[item] = cfg[item] 
                    }
                });
                
                var toolTip = new Ext.ToolTip(qtip_spec);
               
                toolTip.render(Ext.getBody());
                
                toolTip.on('hide', function(tt) {
                    tt.destroy();
                });
                
                toolTip.show();
            }
        }
    },
    
    iFrameCronk: function(cfg) {
        
        if (!'url' in cfg) {
            throw('url XTemplate configuration needed! (parameter name="url")');
        }
        
        return function(grid, rowIndex, colIndex, e) {
            
            var url = Cronk.grid.ColumnRendererUtil.applyXTemplate(grid, rowIndex, cfg.url);
            var title = Cronk.grid.ColumnRendererUtil.applyXTemplate(grid, rowIndex, cfg.title);
            
            var fieldName = grid.getColumnModel().getDataIndex(colIndex);
            if (fieldName == cfg.field) {
                
                if (Ext.isEmpty(cfg.booleanConditionField) == false) {
                    var record = grid.getStore().getAt(rowIndex);
                    if (!Cronk.grid.ColumnRendererUtil.testBooleanCondition(cfg.booleanConditionField, record)) {
                        return;
                    }
                }
                
                var tabPanel = Ext.getCmp("cronk-tabs");
                var cmp = tabPanel.add({
                    'xtype': 'cronk',
                    'title': title,
                    'crname': 'genericIFrame',
                    'params': {
                        url:  encodeURI(url)
                    },
                    'closable':true
                });
                tabPanel.doLayout();
            
                if (!Ext.isEmpty(cfg.activateOnClick) && cfg.activateOnClick) {
                    tabPanel.setActiveTab(cmp);
                }
            
            }
                
        }
    }
};
