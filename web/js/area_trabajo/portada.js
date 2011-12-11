var workspace = function(){
    var assets_tab = {
        title: 'Activos',
        id: 'assets_general',
        iconCls: 'x-icon-configuration',
        style: 'padding: 10px;',
        items: [{
            frame: true,
            autoWidth: true,
            layout: 'form',
            padding: '10px 10px 10px 10px',
            items: [generateRegister(ASSETS_ACCOUNT)]
        }]
    };
    
    var equity_tab = {
        title: 'Patrimonio',
        id: 'equity_general',
        iconCls: 'x-icon-configuration',
        style: 'padding: 10px;',
        items: [{
            frame: true,
            autoWidth: true,
            layout: 'form',
            padding: '10px 10px 10px 10px',
            items: [generateRegister(EQUITY_ACCOUNT)]
        }]
    };
    
    var expenses_tab = {
        title: 'Gastos',
        id: 'expenses_general',
        iconCls: 'x-icon-configuration',
        style: 'padding: 10px;',
        items: [{
            frame: true,
            autoWidth: true,
            layout: 'form',
            padding: '10px 10px 10px 10px',
            items: [generateRegister(EXPENSES_ACCOUNT)]
        }]
    };
    
    var income_tab = {
        title: 'Ingresos',
        id: 'income_general',
        iconCls: 'x-icon-configuration',
        style: 'padding: 10px;',
        items: [{
            frame: true,
            autoWidth: true,
            layout: 'form',
            padding: '10px 10px 10px 10px',
            items: [generateRegister(INCOME_ACCOUNT)]
        }]
    };
    
    var liabilities_tab = {
        title: 'Pasivos',
        id: 'liabilities_general',
        iconCls: 'x-icon-configuration',
        style: 'padding: 10px;',
        items: [{
            frame: true,
            autoWidth: true,
            layout: 'form',
            padding: '10px 10px 10px 10px',
            items: [generateRegister(LIABILITIES_ACCOUNT)]
        }]
    };
    
    var accounting_grouptab = new Ext.ux.GroupTab({
        id: 'accounting',
        expanded: true,
        border: false,
        items: [{
            title: 'Contabilidad',
            style: 'padding: 10px;',
            items: [{
                frame: true,
                autoWidth: true,
                layout: 'form',
                padding: '10px 10px 10px 10px',
                html: 'Módulo de contabilidad'
            }]
        }, assets_tab, equity_tab, expenses_tab, income_tab, liabilities_tab]
    });
    
    var loans_tab = {
        title: 'Préstamos',
        id: 'loans_general',
        iconCls: 'x-icon-configuration',
        style: 'padding: 10px;',
        items: [{
            frame: true,
            autoWidth: true,
            layout: 'form',
            padding: '10px 10px 10px 10px',
            items: [generateLoansGrid()]
        }]
    };
    
    var contacts_tab = {
        title: 'Contactos',
        id: 'contacts_general',
        iconCls: 'x-icon-configuration',
        style: 'padding: 10px;',
        items: [{
            frame: true,
            autoWidth: true,
            layout: 'form',
            padding: '10px 10px 10px 10px',
            items: [generateContactsGrid()]
        }]
    };
    
    var business_grouptab = new Ext.ux.GroupTab({
        id: 'business',
        expanded: true,
        border: false,
        items: [{
            title: 'Negocios',
            style: 'padding: 10px;',
            items: [{
                frame: true,
                autoWidth: true,
                layout: 'form',
                padding: '10px 10px 10px 10px',
                html: 'Módulo de negocios'
            }]
        }, loans_tab, contacts_tab]
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
            items: [accounting_grouptab, business_grouptab],
            listeners: {
                tabchange: function(activeTab){
                    panel = activeTab.activeGroup.activeTab.items.first().items.first();
                    if (panel) {
                        panel.fireEvent('activate');
                    }
                }
            }
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
        text: 'Cerrar sesión',
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
