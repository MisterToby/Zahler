Ext.Loader.setConfig({
    enabled : true
});
Ext.Loader.setPath('Ext.ux', '../ux');

Ext.onReady(function() {
    // Define our data model
    Ext.define('Transaction', {
        extend : 'Ext.data.Model',
        fields : [{
            name : 'id'
        }, {
            name : 'date',
            type : 'date',
            dateFormat : 'Y-m-d'
        }, {
            name : 'traDescription'
        }, {
            name : 'account_id'
        }, {
            name : 'debitAmount'
        }, {
            name : 'creditAmount'
        }, {
            name : 'balance'
        }]
    });

    // create the Data Store
    var store = Ext.create('Ext.data.Store', {
        // destroy the store if the grid is destroyed
        autoDestroy : true,
        model : 'Transaction',
        proxy : {
            type : 'ajax',
            url : prefijoUrl + 'transaction/retrieve/' + accId,
            reader : {
                type : 'json',
                root : 'rows'
            }
        },
        listeners : {
            datachanged : function() {
                for (var i = 0; i < store.getCount(); i++) {
                    var record = store.getAt(i);
                    if (record.get('id') == '') {
                        return true;
                    }
                };
                store.loadData([['', '']], true);
            }
        }
    });

    Ext.define('Account', {
        extend : 'Ext.data.Model',
        fields : [{
            name : 'id'
        }, {
            name : 'accName'
        }, {
            name : 'accActType'
        }, {
            name : 'fullName',
            convert : function(value, record) {
                return record.data.accActType.actName + ':' + record.data.accName;
            }
        }]
    });

    var storeAccounts = Ext.create('Ext.data.Store', {
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

    storeAccounts.load({
        callback : function() {
            store.load();
        }
    });

    var rowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        clicksToMoveEditor : 1,
        clicksToEdit : 1,
        autoCancel : false,
        errorSummary : false,
        listeners : {
            edit : function(editor, e) {
                var debitAmount = e.record.get('debitAmount');
                var creditAmount = e.record.get('creditAmount');
                var amount = 0;
                if (debitAmount > creditAmount) {
                    amount = debitAmount - creditAmount;
                    traAccDebit = accId;
                    traAccCredit = e.record.get('account_id');
                } else {
                    amount = creditAmount - debitAmount;
                    traAccDebit = e.record.get('account_id');
                    traAccCredit = accId;
                }
                var params = {
                    'zahler_zahlerbundle_transaction[traDate][year]' : Ext.Date.format(e.record.get('date'), 'Y'),
                    'zahler_zahlerbundle_transaction[traDate][month]' : parseInt(Ext.Date.format(e.record.get('date'), 'm')),
                    'zahler_zahlerbundle_transaction[traDate][day]' : parseInt(Ext.Date.format(e.record.get('date'), 'd')),
                    'zahler_zahlerbundle_transaction[traDescription]' : e.record.get('traDescription'),
                    'zahler_zahlerbundle_transaction[traAccCredit]' : traAccCredit,
                    'zahler_zahlerbundle_transaction[traAccDebit]' : traAccDebit,
                    'zahler_zahlerbundle_transaction[traAmount]' : amount
                };
                if (e.record.get('id') == '') {
                    Ext.Ajax.request({
                        url : prefijoUrl + 'transaction/create/',
                        params : params,
                        success : function(response) {
                            var text = response.responseText;
                            store.load();
                        },
                        failure : function(response) {
                            var text = response.responseText;
                            store.load();
                        }
                    });
                }
            }
        }
    });

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
            header : 'Date',
            dataIndex : 'date',
            flex : 1,
            renderer : Ext.util.Format.dateRenderer('Y-m-d'),
            editor : {
                xtype : 'datefield',
                allowBlank : false,
                format : 'Y-m-d'
            }
        }, {
            header : 'Description',
            dataIndex : 'traDescription',
            flex : 2,
            editor : {
                allowBlank : false
            }
        }, {
            header : 'Account',
            dataIndex : 'account_id',
            flex : 2,
            renderer : function(value) {
                var record = storeAccounts.findRecord('id', value);
                if (record != null) {
                    return record.get('fullName');
                } else {
                    return value;
                }
            },
            editor : {
                xtype : 'combo',
                allowBlank : false,
                store : storeAccounts,
                valueField : 'id',
                displayField : 'fullName',
                queryMode : 'local'
            }
        }, {
            header : 'Debit',
            dataIndex : 'debitAmount',
            flex : 1,
            editor : {
                allowBlank : false
            }
        }, {
            header : 'Credit',
            dataIndex : 'creditAmount',
            flex : 1,
            editor : {
                allowBlank : false
            }
        }, {
            header : 'Balance',
            dataIndex : 'balance',
            flex : 1
        }],
        width : 600,
        height : 400,
        title : 'Employee Salaries',
        frame : true,
        plugins : [rowEditing]
    });

    Ext.create('Ext.container.Viewport', {
        layout : 'border',
        items : [grid]
    });
});
