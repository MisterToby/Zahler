var workspace = function(){
    var assets_grouptab = new Ext.ux.GroupTab({
        id: 'assets',
        expanded: true,
        border: false,
        items: [{
            title: 'Assets',
            id: 'assets_general',
            iconCls: 'x-icon-configuration',
            tabTip: 'Assets management',
            style: 'padding: 10px;',
            items: [{
                frame: true
                //                ,
                //                autoLoad: {
                //                    url: webUrlFolder + 'assets',
                //                    scripts: true,
                //                    scope: this
                //                }
            }]
        } //        , {
        //            title: 'Informaci&oacute;n Financiera',
        //            id: 'acueducto_financiera',
        //            iconCls: 'x-icon-templates',
        //            tabTip: 'Informaci&oacute;n Financiera',
        //            style: 'padding: 10px; ',
        //            items: [{
        //                frame: true,
        //                autoLoad: {
        //                    url: webUrlFolder + 'acueducto_administrativafinanciera',
        //                    scripts: true,
        //                    scope: this
        //                }
        //            }]
        //        }, {
        //            title: 'Informaci&oacute;n Comercial',
        //            iconCls: 'x-icon-templates',
        //            id: 'acueducto_comercial',
        //            tabTip: 'Informaci&oacute;n Comercial',
        //            style: 'padding: 10px;',
        //            items: [{
        //                autoScroll: true,
        //                frame: true,
        //                autoLoad: {
        //                    url: webUrlFolder + 'acueducto_comercial',
        //                    scripts: true,
        //                    scope: this
        //                }
        //            }]
        //        }, {
        //            title: 'T&eacute;cnico-Operativa',
        //            iconCls: 'x-icon-templates',
        //            id: 'acueducto_tecnicooperativa',
        //            tabTip: 'Informaci&oacute;n <br/>T&eacute;cnico-Operativa',
        //            style: 'padding: 10px;',
        //            items: [{
        //                autoScroll: true,
        //                frame: true,
        //                autoLoad: {
        //                    url: webUrlFolder + 'acueducto_tecnicooperativa',
        //                    scripts: true,
        //                    scope: this
        //                }
        //            }]
        //        }, {
        //            title: 'Microcuencas',
        //            iconCls: 'x-icon-templates',
        //            id: 'acueducto_microcuenca',
        //            tabTip: 'Microcuencas',
        //            style: 'padding: 10px;',
        //            items: [{
        //                autoScroll: true,
        //                frame: true,
        //                autoLoad: {
        //                    url: webUrlFolder + 'acueducto_microcuencasporperiodoporprestadorservicio',
        //                    scripts: true,
        //                    scope: this
        //                }
        //            }]
        //        }
        ],
        listeners: {
            'activate': function(){
                this.setActiveTab(0);
            }
        }
    });
    
    var equity_grouptab = new Ext.ux.GroupTab({
        id: 'equity',
        expanded: true,
        border: false,
        items: [{
            title: 'Equity',
            id: 'equity_general',
            iconCls: 'x-icon-configuration',
            tabTip: 'Equity management',
            style: 'padding: 10px;',
            items: [{
                frame: true,
                //                autoLoad: {
                //                    url: webUrlFolder + 'assets',
                //                    scripts: true,
                //                    scope: this
                //                }
            }]
        }]
    });
    
    var income_grouptab = new Ext.ux.GroupTab({
        id: 'income',
        expanded: true,
        border: false,
        items: [{
            title: 'Income',
            id: 'income_general',
            iconCls: 'x-icon-configuration',
            tabTip: 'Income management',
            style: 'padding: 10px;',
            items: [{
                frame: true,
                //                autoLoad: {
                //                    url: webUrlFolder + 'assets',
                //                    scripts: true,
                //                    scope: this
                //                }
            }]
        }]
    });
    
    var expenses_grouptab = new Ext.ux.GroupTab({
        id: 'expenses',
        expanded: true,
        border: false,
        items: [{
            title: 'Expenses',
            id: 'expenses_general',
            iconCls: 'x-icon-configuration',
            tabTip: 'Expenses management',
            style: 'padding: 10px;',
            items: [{
                frame: true,
                //                autoLoad: {
                //                    url: webUrlFolder + 'assets',
                //                    scripts: true,
                //                    scope: this
                //                }
            }]
        }]
    });
    
    var liabilities_grouptab = new Ext.ux.GroupTab({
        id: 'liabilities',
        expanded: true,
        border: false,
        items: [{
            title: 'Liabilities',
            id: 'liabilities_general',
            iconCls: 'x-icon-configuration',
            tabTip: 'Liabilities management',
            style: 'padding: 10px;',
            items: [{
                frame: true,
                //                autoLoad: {
                //                    url: webUrlFolder + 'assets',
                //                    scripts: true,
                //                    scope: this
                //                }
            }]
        }]
    });
    
    var viewport = new Ext.Viewport({
        layout: 'border',
        //region: 'center',
        //                cls: 'nomostrartab',
        items: [{
            frame: true,
            region: 'north',
            baseCls: 'x-bubble',
            layout: 'fit',
            padding: 5,
            margins: '10 10 0 10',
            contentEl: 'titulo',
            height: 65
        }, {
            region: 'center',
            xtype: 'grouptabpanel',
            deferredRender: false,
            id: 'panel_servicios',
            tabWidth: 170,
            activeGroup: 0,
            items: [assets_grouptab, equity_grouptab, expenses_grouptab, income_grouptab, liabilities_grouptab]
        }, {
            frame: true,
            baseCls: 'x-bubble',
            //                    cls: 'nomostrar',
            region: 'south',
            height: 65,
            margins: '0 10 0 10',
            html: ''
        }]
    });
    
    var logout_button = new Ext.Button({
        text: 'Logout',
        renderTo: 'logout_button',
        handler: function(){
            Ext.Ajax.request({
                url: getAbsoluteUrl('authentication', 'logOut'),
                success: function(response, action){
                    obj = Ext.util.JSON.decode(response.responseText);
                    if (obj.success) {
                        window.location = getAbsoluteUrl('authentication', 'index');
                    }
                },
                failure: function(response){
                    Ext.Msg.alert('Error', 'It was not possible to establish a connection to the server. Try again later');
                }
            });
        }
    });
};
