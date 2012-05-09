/*global Ext: false, Icinga: false, AppKit: false, _: false, Cronk: false */
Ext.ns('Icinga.Cronks.System.MonitorPerformance');

(function() {
	"use strict";
	
	Icinga.Cronks.System.MonitorPerformance.Cronk = Ext.extend(Ext.Panel, {
		layout: 'column',
		
		hostThreshold: 0,
		serviceThreshold: 0,
		refreshInterval: 60,
		dataProvider: null,
		storeId: 'overall-status-store',
		task: {},
		
		constructor: function(c) {
			Icinga.Cronks.System.StatusOverall.Cronk.superclass.constructor.call(this, c);
		},
		
		initComponent: function() {
			
			Icinga.Cronks.System.StatusOverall.Cronk.superclass.initComponent.call(this);
			
			this.initDataView();
			
			this.initRefreshButton();
			
			this.task = {
                run: this.refresh,
                interval: (this.refreshInterval*1000),
                scope: this
            };
			
			if (this.refreshInterval) {
				this.startRefreshTask();
			} else {
				throw("No interval was set!");
			}
		},
		
		startRefreshTask: function() {
			AppKit.getTr().start(this.task);
		},
		
		refresh: function() {
			this.store.reload();
		},
		
		initDataView: function() {
            this.viewTemplate = new Ext.XTemplate(
            '<tpl for=".">',

            '<div class="float-container clearfix icinga-monitor-performance" style="width: 300px;">',

            '<div class="icinga-monitor-performance-container-50">',

                '<div class="clearfix icinga-monitor-performance-container">',
                    '<div title="' + _('Hosts (active/passive/disabled)') + '" class="key icinga-icon-host"></div>',
                    '<div class="value">{host_checks_active} / {host_checks_passive} / {host_checks_disabled}</div>',
                '</div>',

                '<div class="clearfix icinga-monitor-performance-container">',
                    '<div title="' + _('Host execution time (min/avg/max)') + '" class="key icinga-icon-execution-time"></div>',
                    '<div class="value">{host_execution_time_min} / {host_execution_time_avg} / {host_execution_time_max}</div>',
                '</div>',

                '<div class="clearfix icinga-monitor-performance-container">',
                    '<div title="' + _('Host latency (min/avg/max)') + '" class="key icinga-icon-latency"></div>',
                    '<div class="value">{host_latency_min} / ',
                    '<tpl if="host_latency_avg &gt; '+this.hostThreshold+'"><span style="color:red" ext:qtip="Threshold reached"> {host_latency_avg} </span></tpl>',
                    '<tpl if="host_latency_avg &lt;= '+this.hostThreshold+'">{host_latency_avg} </tpl>',
                    ' / {host_latency_max}</div>',
                '</div>',

            '</div>',

            '<div class="icinga-monitor-performance-container-50">',

                '<div class="clearfix icinga-monitor-performance-container">',
                    '<div title="' + _('Services (active/passive/disabled)') + '" class="key icinga-icon-service"></div>',
                    '<div class="value">{service_checks_active} / {service_checks_passive} /  {service_checks_disabled}</div>',
                '</div>',

                '<div class="clearfix icinga-monitor-performance-container">',
                    '<div title="' + _('Service execution (min/avg/max)') + '" class="key icinga-icon-execution-time"></div>',
                    '<div class="value">{service_execution_time_min} / {service_execution_time_avg} / {service_execution_time_max}</div>',
                '</div>',

                '<div class="clearfix icinga-monitor-performance-container">',
                    '<div title="' + _('Service latency (min/avg/max)') + '" class="key icinga-icon-latency"></div>',
                    '<div class="value">{service_latency_min} / ',
                    '<tpl if="service_latency_avg &gt; '+this.serviceThreshold+'"><span style="color:red" ext:qtip="Threshold reached"> {service_latency_avg} </span></tpl>',
                    '<tpl if="service_latency_avg &lt;= '+this.serviceThreshold+'">{service_latency_avg} </tpl>',
                    ' / {service_latency_avg}</div>',
                '</div>',

            '</div>',

            '</div>',

            '</tpl>'
            );
            
            this.store = new Ext.data.JsonStore({
                url: this.dataProvider,
                storeId: this.storeId
            });
            
            this.view = new Ext.DataView({
                store: this.store,
                tpl: this.viewTemplate,
                itemSelector:'div.icinga-monitor-performance-container',
                emptyText: _('No performance data available')
            });
            
            this.add(this.view);
		},
		
		initRefreshButton: function() {
            this.refreshButton = new Ext.Button({
                iconCls: 'icinga-action-refresh',
                handler: function(b, e) {
                    this.store.reload();
                },
                tooltip: _('Reload performance view'),
                scope: this
            });
            
            this.store.on('beforeload', function (store, records, options) {
                this.refreshButton.setDisabled(true);
            }, this);
            
            this.store.on('load', function (store, records, options) {
                this.refreshButton.setDisabled(false);
            }, this);
            
            this.add({
                xtype: 'panel',
                width: 30,
                height: 48,
                layout: 'vbox',
                layoutConfig: {
                    pack: 'top',
                    align: 'center'
                },
                items : this.refreshButton
            });
		}
		
	});
})();