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
            name: 'transfer',
            type: 'string'
        }, {
            name: 'debit',
            type: 'string'
        }, {
            name: 'credit',
            type: 'string'
        }])
    });
    
    var roweditor = new Ext.ux.grid.RowEditor({
        saveText: 'Save',
        cancelText: 'Cancel',
        listeners: {
            'afteredit': function(){
                //                registro = acu_tanque_almacenamiento_gridpanel.getSelectionModel().getSelected();
                //                Ext.Ajax.request({
                //                    url: getAbsoluteUrl('acueducto_tanque_almacenamiento', 'actualizarTanque'),
                //                    failure: function(){
                //                        acu_tanque_almacenamiento_datastore.load();
                //                    },
                //                    params: {
                //                        tan_id: registro.get('tan_id'),
                //                        tan_volumen: registro.get('tan_volumen'),
                //                        tan_estado_id: registro.get('tan_estado_id'),
                //                        tan_bypass_directo_red: registro.get('tan_bypass_directo_red'),
                //                        tan_presencia_valvula_control: registro.get('tan_presencia_valvula_control'),
                //                        tan_proteccion_tapa: registro.get('tan_proteccion_tapa'),
                //                        tan_cerramiento_lote: registro.get('tan_cerramiento_lote'),
                //                        tan_ventosa_salida: registro.get('tan_ventosa_salida'),
                //                        tan_macro_medidor: registro.get('tan_macro_medidor')
                //                    }
                //                });
            },
            'canceledit': function(){
                //                acu_tanque_almacenamiento_datastore.load();
            }
        }
    });
    
    var gridpanel = new Ext.grid.GridPanel({
        store: datastore,
        frame: true,
        plugins: [roweditor],
        border: false,
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
            width: 80,
            dataIndex: 'date',
            editor: new Ext.form.DateField({
                emptyText: false
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
            dataIndex: 'transfer',
            editor: new Ext.form.ComboBox({
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
        store: accounts_datastore,
        displayField: 'account_name',
        valueField: 'account_id',
        mode: 'local',
        listeners: {
            select: function(){
                datastore.load({
                    params: {
                        account_id: combobox.getValue()
                    }
                });
            }
        }
    });
    
    return {
        height: 400,
        autoWidth: true,
        layout: 'form',
        items: [combobox, {
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
                        'transfer': '',
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
