var generateAnnuitiesGrid = function(){
    var datastore = new Ext.data.Store({
        proxy: new Ext.data.HttpProxy({
            url: getAbsoluteUrl('annuity', 'getList'),
            method: 'POST'
        }),
        reader: new Ext.data.JsonReader({
            root: 'data',
        }, [{
            name: 'annuity_id',
            type: 'string'
        }, {
            name: 'contact_id',
            type: 'string'
        }, {
            name: 'transaction_id',
            type: 'string'
        }, {
            name: 'interest_rate',
            type: 'float'
        }, {
            name: 'loan_term',
            type: 'int'
        }, {
            name: 'loan_amount',
            type: 'float'
        }])
    });
    
    datastore.load();
    
    var roweditor = new Ext.ux.grid.RowEditor({
        saveText: 'Save',
        cancelText: 'Cancel',
        errorSummary: false,
        onKey: function(f, e){
            if (e.getKey() === e.ENTER && this.isValid()) {
                this.stopEditing(true);
                e.stopPropagation();
            }
        },
        listeners: {
            'afteredit': function(){
                var record = gridpanel.getSelectionModel().getSelected();
                Ext.Ajax.request({
                    url: getAbsoluteUrl('annuity', 'create'),
                    failure: function(){
                        datastore.load();
                    },
                    success: function(){
                        datastore.load();
                    },
                    params: {
                        'annuity_id': record.get('annuity_id'),
                        'contact_id': record.get('contact_id'),
                        'transaction_id': record.get('transaction_id'),
                        'interest_rate': record.get('interest_rate'),
                        'loan_term': record.get('loan_term'),
                        'loan_amount': record.get('loan_amount')
                    }
                });
            },
            'canceledit': function(){
                datastore.load();
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
            header: 'Annuity Id',
            width: 110,
            dataIndex: 'annuity_id'
        }, {
            header: 'Contact Id',
            width: 110,
            dataIndex: 'contact_id'
        }, {
            header: 'Transaction Id',
            width: 110,
            dataIndex: 'transaction_id'
        }, {
            header: 'Interest rate',
            width: 95,
            dataIndex: 'interest_rate',
            editor: new Ext.form.TextField({})
        }, {
            header: 'Loan term',
            width: 95,
            dataIndex: 'loan_term',
            editor: new Ext.form.TextField({})
        }, {
            header: 'Loan amount',
            width: 180,
            dataIndex: 'loan_amount',
            editor: new Ext.form.TextField({})
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
            layout: 'form',
            bodyStyle: 'padding-top: 10px;',
            items: [gridpanel],
            buttonAlign: 'left',
            buttons: [{
                text: 'Add',
                handler: function(){
                    var row = new gridpanel.store.recordType({
                        'annuity_id': '',
                        'contact_id': '',
                        'transaction_id': '',
                        'interest_rate': '',
                        'loan_term': '',
                        'loan_amount': ''
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
                    if (gridpanel.getSelectionModel().hasSelection()) {
                        var record = gridpanel.getSelectionModel().getSelected();
                        var selectedContactId = record.get('contact_id');
                        Ext.Ajax.request({
                            url: getAbsoluteUrl('annuity', 'delete'),
                            failure: function(){
                                datastore.load();
                            },
                            success: function(){
                                datastore.load();
                            },
                            params: {
                                id: selectedContactId
                            }
                        });
                    }
                    else {
                        alert('Select an annuity first');
                    }
                }
            }]
        }]
    };
}
