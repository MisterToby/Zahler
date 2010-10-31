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
                frame: true,
                autoWidth: true,
                layout: 'form',
                padding: '10px 10px 10px 10px',
                items: [generateRegister(ASSETS_ACCOUNT)]
            }]
        }],
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
                autoWidth: true,
                layout: 'form',
                padding: '10px 10px 10px 10px',
                items: [generateRegister(EQUITY_ACCOUNT)]
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
                autoWidth: true,
                layout: 'form',
                padding: '10px 10px 10px 10px',
                items: [generateRegister(EXPENSES_ACCOUNT)]
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
                autoWidth: true,
                layout: 'form',
                padding: '10px 10px 10px 10px',
                items: [generateRegister(INCOME_ACCOUNT)]
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
                autoWidth: true,
                layout: 'form',
                padding: '10px 10px 10px 10px',
                items: [generateRegister(LIABILITIES_ACCOUNT)]
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
