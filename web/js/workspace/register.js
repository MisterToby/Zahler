var generateRegister = function(accountType){
    var datastore = new Ext.data.Store({
        proxy: new Ext.data.HttpProxy({
            url: getAbsoluteUrl('transaction', 'getTransactionList'),
            method: 'POST'
        }),
        reader: new Ext.data.JsonReader({
            root: 'data',
        }, [{
            name: 'id',
            type: 'string'
        }, {
            name: 'date',
            type: 'string'
        }, {
            name: 'reference',
            type: 'string'
        }, {
            name: 'description',
            type: 'string'
        }, {
            name: 'target_account_id',
            type: 'string'
        }, {
            name: 'debit',
            type: 'string'
        }, {
            name: 'credit',
            type: 'string'
        }])
    });
    
    var accounts_datastore = new Ext.data.Store({
        proxy: new Ext.data.HttpProxy({
            url: getAbsoluteUrl('account', 'getAccountList'),
            method: 'POST'
        }),
        baseParams: {
            account_type: accountType
        },
        reader: new Ext.data.JsonReader({
            root: 'data',
        }, [{
            name: 'account_id',
            type: 'integer'
        }, {
            name: 'account_name',
            type: 'string'
        }])
    });
    
    accounts_datastore.load();
    
    var combobox = new Ext.form.ComboBox({
        fieldLabel: 'Account',
        id: 'accounts_combobox',
        store: accounts_datastore,
        displayField: 'account_name',
        valueField: 'account_id',
        mode: 'local',
        forceSelection: true,
        listeners: {
            select: function(){
                datastore.load({
                    params: {
                        account_id: combobox.getValue()
                    },
                    callback: function(){
                        gridpanel.enable();
                    }
                });
            }
        }
    });
    
    var roweditor = new Ext.ux.grid.RowEditor({
        saveText: 'Save',
        cancelText: 'Cancel',
        listeners: {
            'afteredit': function(){
                var record = gridpanel.getSelectionModel().getSelected();
                var accountId = combobox.getValue();
                Ext.Ajax.request({
                    url: getAbsoluteUrl('transaction', 'create'),
                    failure: function(){
                        datastore.load({
                            params: {
                                account_id: accountId
                            }
                        });
                    },
                    params: {
                        date: record.get('date'),
                        reference: record.get('reference'),
                        description: record.get('description'),
                        'account1_id': combobox.getValue(),
                        'account2_id': record.get('target_account_id'),
                        debit: record.get('debit'),
                        credit: record.get('credit')
                    }
                });
            },
            'canceledit': function(){
                var accountId = combobox.getValue();
                datastore.load({
                    params: {
                        account_id: accountId
                    }
                });
            }
        }
    });
    
    var gridpanel = new Ext.grid.GridPanel({
        store: datastore,
        frame: true,
        plugins: [roweditor],
        border: false,
        disabled: true,
        selModel: new Ext.grid.RowSelectionModel({
            singleSelect: true
        }),
        columns: [{
            header: 'Id',
            width: 110,
            dataIndex: 'id'
        }, {
            id: 'date',
            header: "Date",
            width: 90,
            dataIndex: 'date',
            renderer: Ext.util.Format.dateRenderer('d-m-Y'),
            editor: new Ext.form.DateField({
                format: 'd-m-Y',
                allowBlank: false
            })
        }, {
            header: 'Reference',
            width: 95,
            dataIndex: 'reference',
            editor: new Ext.form.TextField({})
        }, {
            header: 'Description',
            width: 180,
            dataIndex: 'description',
            editor: new Ext.form.TextField({})
        }, {
            header: 'Transfer',
            width: 200,
            dataIndex: 'target_account_id',
            editor: new Ext.form.ComboBox({
                store: accounts_datastore,
                displayField: 'account_name',
                valueField: 'account_id',
                mode: 'local',
                forceSelection: true,
                allowBlank: false
            })
        }, {
            header: 'Debit',
            width: 110,
            dataIndex: 'debit',
            editor: new Ext.form.NumberField({
                allowBlank: false
            })
        }, {
            header: 'Credit',
            width: 110,
            dataIndex: 'credit',
            editor: new Ext.form.NumberField({
                allowBlank: false
            })
        }],
        width: '100%',
        height: 240,
        wrap: true,
        stripeRows: true,
        clicksToEdit: 1
    });
    
    return {
        height: 400,
        autoWidth: true,
        layout: 'form',
        items: [{
            layout: 'column',
            items: [{
                layout: 'form',
                width: 350,
                items: [combobox]
            }, {
                layout: 'form',
                bodyStyle: 'padding-left: 10px;',
                items: [{
                    xtype: 'button',
                    text: 'Add',
                    width: 80,
                    handler: function(){
                        Ext.Msg.prompt('Create account', "Input the account's name", function(idButton, text){
                            if (idButton = 'ok' && text != '') {
                                Ext.Ajax.request({
                                    url: getAbsoluteUrl('account', 'create'),
                                    params: {
                                        'account_name': text,
                                        'account_type': accountType
                                    },
                                    success: function(response){
                                        if (response.responseText == 'ok') {
                                            accounts_datastore.load();
                                            alert('Account created');
                                        }
                                    }
                                });
                            }
                        });
                    }
                }]
            }]
        }, {
            layout: 'form',
            bodyStyle: 'padding-top: 10px;',
            items: [gridpanel],
            buttonAlign: 'left',
            buttons: [{
                text: 'Add',
                handler: function(){
                    var row = new gridpanel.store.recordType({
                        'id': '',
                        'date': '',
                        'reference': '',
                        'description': '',
                        'target_account_id': '',
                        'debit': '',
                        'credit': '',
                        'balance': ''
                    });
                    gridpanel.getSelectionModel().clearSelections();
                    roweditor.stopEditing();
                    gridpanel.store.insert(0, row);
                    gridpanel.getSelectionModel().selectRow(0);
                    roweditor.startEditing(0);
                }
            }, {
                text: 'Delete',
                handler: function(){
                    alert('Delete');
                }
            }]
        }]
    };
}
