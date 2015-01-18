Ext.Loader.setConfig({
    enabled : true
});
Ext.Loader.setPath('Ext.ux', '../ux');

Ext.onReady(function() {
    // Define our data model
    Ext.define('Account', {
        extend : 'Ext.data.Model',
        fields : [{
            name : 'id'
        }, {
            name : 'actname'
        }, {
            name : 'accname'
        }, {
            name : 'balance',
            type : 'float'
        }]
    });

    // create the Data Store
    var store = Ext.create('Ext.data.Store', {
        // destroy the store if the grid is destroyed
        autoDestroy : true,
        model : 'Account',
        groupField : 'actname',
        proxy : {
            type : 'ajax',
            url : prefijoUrl + 'account/retrieve',
            reader : {
                type : 'json',
                root : 'rows'
            }
        }
    });

    store.load();

    // create the grid and specify what field you want
    // to use for the editor at each column.
    var grid = Ext.create('Ext.grid.Panel', {
        region : 'center',
        store : store,
        features : [{
            ftype : 'groupingsummary',
            groupHeaderTpl : '{name}'
        }],
        columns : [{
            header : 'Id',
            dataIndex : 'id',
            flex : 1,
            summaryType : 'count',
            summaryRenderer : function(value) {
                return '(' + value + ' accounts)';
            }
        }, {
            header : 'Type',
            dataIndex : 'actname',
            flex : 1,
            hidden: true
        }, {
            header : 'Name',
            dataIndex : 'accname',
            flex : 2
        }, {
            header : 'Balance',
            dataIndex : 'balance',
            flex : 1,
            align : 'right',
            renderer : Ext.util.Format.usMoney,
            summaryType : 'sum',
            summaryRenderer : function(value) {
                return Ext.util.Format.usMoney(value);
            }
        }, {
            xtype : 'actioncolumn',
            width : 30,
            sortable : false,
            align : 'right',
            items : [{
                icon : resourcesPath + 'images/open.png',
                tooltip : 'Open account',
                handler : function(grid, rowIndex, colIndex) {
                    var record = store.getAt(rowIndex);
                    window.open(prefijoUrl + 'transaction/js/' + record.get('id'));
                }
            }]
        }],
        tbar : ['->', {
            xtype : 'button',
            iconCls : 'logout',
            scale : 'large',
            text : 'Log out',
            handler : function() {
                window.location = prefijoUrl + 'logout';
            }
        }],
        width : 600,
        height : 400,
        // title : 'Transactions',
        frame : true,
        listeners : {
            itemdblclick : function(view, record) {
                window.open(prefijoUrl + 'transaction/js/' + record.get('id'));
            }
        }
    });

    Ext.create('Ext.container.Viewport', {
        layout : 'border',
        items : [grid]
    });
});
