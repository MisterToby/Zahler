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
        }, {
            name : 'traAccCredit'
        }, {
            name : 'traAccDebit'
        }, {
            name : 'traAmount'
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
                store.loadData([['', new Date(), '', '', 0, 0]], true);
            }
        }
    });

    Ext.define('Account', {
        extend : 'Ext.data.Model',
        fields : [{
            name : 'id'
        }, {
            name : 'accname'
        }, {
            name : 'actname'
        }, {
            name : 'fullName',
            convert : function(value, record) {
                return record.data.actname + ':' + record.data.accname;
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
                var debitAmount = parseFloat(e.record.get('debitAmount'));
                var creditAmount = parseFloat(e.record.get('creditAmount'));
                var amount = 0;
                var traAccDebit = null;
                var traAccDebit = null;
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
                } else {
                    params._method = 'PUT';
                    Ext.Ajax.request({
                        url : prefijoUrl + 'transaction/update/' + e.record.get('id'),
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

    storeDescription = Ext.create('Ext.data.Store', {
        autoDestroy : true,
        model : 'Transaction',
        proxy : {
            type : 'ajax',
            url : prefijoUrl + 'transaction/query/',
            reader : {
                type : 'json',
                root : 'rows'
            },
            extraParams : {
                account_id : accId
            }
        }
    });

    // create the grid and specify what field you want
    // to use for the editor at each column.
    var grid = Ext.create('Ext.grid.Panel', {
        region : 'center',
        store : store,
        tbar : [{
            xtype : 'textfield',
            fieldLabel : 'Account',
            labelWidth : 50,
            width : 300,
            readOnly : true,
            value: accName
        }, '->', '-', {
            xtype : 'button',
            iconCls : 'refresh',
            scale : 'large',
            text : 'Refresh',
            handler : function() {
                store.load();
            }
        }, '-', {
            xtype : 'button',
            iconCls : 'logout',
            scale : 'large',
            text : 'Log out',
            handler : function() {
                window.location = prefijoUrl + 'logout';
            }
        }],
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
                xtype : 'combo',
                allowBlank : false,
                store : storeDescription,
                displayField : 'traDescription',
                valueField : 'traDescription',
                typeAhead : false,
                hideLabel : true,
                hideTrigger : true,
                anchor : '100%',
                listeners : {
                    select : function(combo, records) {
                        var record = records[0];
                        console.log(records);
                        var debitAmount_numberfield = Ext.getCmp('debitAmount_numberfield');
                        var creditAmount_numberfield = Ext.getCmp('creditAmount_numberfield');
                        var account_id_combo = Ext.getCmp('account_id_combo');
                        if (record.get('traAccDebit').id == accId) {
                            account_id_combo.setValue(record.get('traAccCredit').id);
                            debitAmount_numberfield.setValue(record.get('traAmount'));
                            creditAmount_numberfield.setValue(0);
                        } else if (record.get('traAccCredit').id == accId) {
                            account_id_combo.setValue(record.get('traAccDebit').id);
                            debitAmount_numberfield.setValue(0);
                            creditAmount_numberfield.setValue(record.get('traAmount'));
                        }
                    }
                }
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
                id : 'account_id_combo',
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
            align : 'right',
            renderer : Ext.util.Format.usMoney,
            editor : {
                id : 'debitAmount_numberfield',
                xtype : 'numberfield',
                decimalSeparator : ',',
                allowBlank : false
            }
        }, {
            header : 'Credit',
            dataIndex : 'creditAmount',
            flex : 1,
            align : 'right',
            renderer : Ext.util.Format.usMoney,
            editor : {
                id : 'creditAmount_numberfield',
                xtype : 'numberfield',
                decimalSeparator : ',',
                allowBlank : false
            }
        }, {
            header : 'Balance',
            dataIndex : 'balance',
            flex : 1,
            renderer : Ext.util.Format.usMoney,
            align : 'right'
        }, {
            xtype : 'actioncolumn',
            width : 30,
            sortable : false,
            items : [{
                icon : resourcesPath + 'images/delete.png',
                tooltip : 'Delete transaction',
                handler : function(grid, rowIndex, colIndex) {
                    var callback = function(button) {
                        if (button == 'yes') {
                            var record = store.getAt(rowIndex);
                            var params = {
                                '_method' : 'DELETE'
                            };
                            Ext.Ajax.request({
                                url : prefijoUrl + 'transaction/delete/' + record.get('id'),
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
                    };
                    Ext.MessageBox.confirm('Confirm', 'Are you sure you want to delete this transaction?', callback);
                }
            }]
        }],
        width : 600,
        height : 400,
        // title : 'Transactions',
        frame : true,
        plugins : [rowEditing]
    });

    Ext.create('Ext.container.Viewport', {
        layout : 'border',
        items : [grid]
    });
});
