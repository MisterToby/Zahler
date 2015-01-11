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
            name : 'accActType'
        }, {
            name : 'accName'
        }]
    });

    // create the Data Store
    var store = Ext.create('Ext.data.Store', {
        // destroy the store if the grid is destroyed
        autoDestroy : true,
        model : 'Account',
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
        columns : [{
            header : 'Id',
            dataIndex : 'id',
            flex : 1
        }, {
            header : 'Type',
            dataIndex : 'accActType',
            flex : 1,
            renderer : function(value, metaData, record) {
                return record.get('accActType').actName;
            }
        }, {
            header : 'Name',
            dataIndex : 'accName',
            flex : 2
        }, {
            xtype : 'actioncolumn',
            width : 30,
            sortable : false,
            items : [{
                icon : resourcesPath + 'images/open.png',
                tooltip : 'Open account',
                handler : function(grid, rowIndex, colIndex) {
                    var record = store.getAt(rowIndex);
                    window.open(prefijoUrl + 'transaction/js/' + record.get('id'));
                }
            }]
        }],
        width : 600,
        height : 400,
        // title : 'Transactions',
        frame : true
    });

    Ext.create('Ext.container.Viewport', {
        layout : 'border',
        items : [grid]
    });
});
