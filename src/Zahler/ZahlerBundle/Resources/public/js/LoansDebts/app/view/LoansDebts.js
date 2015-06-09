Ext.define('LoansDebtsApp.view.LoansDebts', {
    extend : 'Ext.panel.Panel',

    requires : ['Ext.grid.Panel', 'Ext.grid.column.Number', 'Ext.grid.View', 'Ext.grid.column.Date'],

    height : 489,
    width : 658,
    title : '',
    region : 'center',

    layout : {
        type : 'vbox',
        align : 'stretch'
    },

    initComponent : function() {
        var me = this;

        Ext.define('Person', {
            extend : 'Ext.data.Model',
            fields : [{
                name : 'id'
            }, {
                name : 'per_name'
            }]
        });

        var pageSize = 10;

        var peopleStore = Ext.create('Ext.data.Store', {
            model : 'Person',
            pageSize : pageSize,
            proxy : {
                type : 'ajax',
                url : prefijoUrl + 'person/retrieve',
                reader : {
                    type : 'json',
                    root : 'rows',
                    totalProperty : 'count'
                }
            },
            autoLoad : true
        });

        Ext.define('Loan', {
            extend : 'Ext.data.Model',
            fields : [{
                name : 'loa_id'
            }, {
                name : 'tra_date',
                type : 'date',
                dateFormat : 'Y-m-d'
            }, {
                name : 'loa_description'
            }, {
                name : 'loa_interest_rate'
            }, {
                name : 'tra_amount'
            }, {
                name : 'tra_acc_id_credit'
            }]
        });

        var loanStore = Ext.create('Ext.data.Store', {
            model : 'Loan',
            pageSize : pageSize,
            proxy : {
                type : 'ajax',
                url : prefijoUrl + 'loan/retrieve',
                reader : {
                    type : 'json',
                    root : 'rows',
                    totalProperty : 'count'
                }
            },
            listeners : {
                datachanged : function() {
                    for (var i = 0; i < loanStore.getCount(); i++) {
                        var record = loanStore.getAt(i);
                        if (record.get('loa_id') == '') {
                            return true;
                        }
                    };
                    loanStore.loadData([['', new Date(), '', '', '', '']], true);
                }
            }
        });

        Ext.define('Debt', {
            extend : 'Ext.data.Model',
            fields : [{
                name : 'deb_id'
            }, {
                name : 'tra_date',
                type : 'date',
                dateFormat : 'Y-m-d'
            }, {
                name : 'deb_description'
            }, {
                name : 'deb_interest_rate'
            }, {
                name : 'tra_amount'
            }, {
                name : 'tra_acc_id_debit'
            }]
        });

        var debtStore = Ext.create('Ext.data.Store', {
            model : 'Debt',
            pageSize : pageSize,
            proxy : {
                type : 'ajax',
                url : prefijoUrl + 'debt/retrieve',
                reader : {
                    type : 'json',
                    root : 'rows',
                    totalProperty : 'count'
                }
            },
            listeners : {
                datachanged : function() {
                    for (var i = 0; i < debtStore.getCount(); i++) {
                        var record = debtStore.getAt(i);
                        if (record.get('deb_id') == '') {
                            return true;
                        }
                    };
                    debtStore.loadData([['', new Date(), '', '', '', '']], true);
                }
            }
        });

        Ext.define('DebtPayment', {
            extend : 'Ext.data.Model',
            fields : [{
                name : 'dep_tra_id'
            }, {
                name : 'tra_date'
            }, {
                name : 'tra_amount'
            }]
        });

        var debtPaymentStore = Ext.create('Ext.data.Store', {
            model : 'DebtPayment',
            pageSize : pageSize,
            proxy : {
                type : 'ajax',
                url : prefijoUrl + 'debtpayment/retrieve',
                reader : {
                    type : 'json',
                    root : 'rows',
                    totalProperty : 'count'
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

        storeAccounts.load();

        Ext.define('LoanPayment', {
            extend : 'Ext.data.Model',
            fields : [{
                name : 'pay_id'
            }, {
                name : 'pay_tra_id'
            }, {
                name : 'tra_date',
                type : 'date',
                dateFormat : 'Y-m-d'
            }, {
                name : 'tra_amount'
            }, {
                name : 'tra_acc_id_debit'
            }, {
                name : 'pay_tra_id_interest'
            }, {
                name : 'interest_amount'
            }]
        });

        var loanPaymentStore = Ext.create('Ext.data.Store', {
            model : 'LoanPayment',
            pageSize : pageSize,
            proxy : {
                type : 'ajax',
                url : prefijoUrl + 'payment/retrieve',
                reader : {
                    type : 'json',
                    root : 'rows',
                    totalProperty : 'count'
                }
            },
            listeners : {
                datachanged : function() {
                    for (var i = 0; i < loanPaymentStore.getCount(); i++) {
                        var record = loanPaymentStore.getAt(i);
                        if (record.get('pay_id') == '') {
                            return true;
                        }
                    };
                    loanPaymentStore.loadData([['', '', new Date(), '']], true);
                }
            }
        });

        var loan_payments_window = Ext.create('Ext.window.Window', {
            title : 'Loan payments',
            width : 829,
            height : 375,
            layout : 'fit',
            closeAction : 'hide',
            items : [{
                xtype : 'gridpanel',
                flex : 1,
                store : loanPaymentStore,
                columns : [{
                    dataIndex : 'pay_tra_id',
                    text : 'Transaction'
                }, {
                    xtype : 'datecolumn',
                    dataIndex : 'tra_date',
                    text : 'Date',
                    format : 'Y-m-d',
                    renderer : Ext.util.Format.dateRenderer('Y-m-d'),
                    editor : {
                        xtype : 'datefield',
                        format : 'Y-m-d',
                    }
                }, {
                    xtype : 'numbercolumn',
                    dataIndex : 'tra_amount',
                    text : 'Amount',
                    editor : {
                        xtype : 'numberfield'
                    }
                }, {
                    header : 'Destination account',
                    dataIndex : 'tra_acc_id_debit',
                    flex : 1,
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
                    dataIndex : 'pay_tra_id_interest',
                    text : 'Interest transaction',
                    width : 112
                }, {
                    xtype : 'numbercolumn',
                    dataIndex : 'interest_amount',
                    text : 'Interest',
                    editor : {
                        xtype : 'numberfield'
                    }
                }, {
                    xtype : 'actioncolumn',
                    width : 30,
                    items : [{
                        icon : resourcesPath + 'images/delete.png',
                        tooltip : 'Delete payment',
                        handler : function(grid, rowIndex, colIndex) {
                            var callback = function(button) {
                                if (button == 'yes') {
                                    var record = loanPaymentStore.getAt(rowIndex);
                                    var params = {
                                        '_method' : 'DELETE'
                                    };
                                    Ext.Ajax.request({
                                        url : prefijoUrl + 'payment/delete/' + record.get('pay_id'),
                                        params : params,
                                        success : function(response) {
                                            var text = response.responseText;
                                            loanPaymentStore.load();
                                        },
                                        failure : function(response) {
                                            var text = response.responseText;
                                            loanPaymentStore.load();
                                        }
                                    });
                                }
                            };
                            Ext.MessageBox.confirm('Confirm', 'Are you sure you want to delete this payment?', callback);
                        }
                    }]
                }],
                dockedItems : [{
                    xtype : 'pagingtoolbar',
                    store : loanPaymentStore,
                    dock : 'bottom',
                    displayInfo : true
                }],
                plugins : [Ext.create('Ext.grid.plugin.RowEditing', {
                    clicksToEdit : 2,
                    listeners : {
                        edit : function(editor, e) {
                            var params = {
                                'zahler_zahlerbundle_payment[date][year]' : Ext.Date.format(e.record.get('tra_date'), 'Y'),
                                'zahler_zahlerbundle_payment[date][month]' : parseInt(Ext.Date.format(e.record.get('tra_date'), 'm')),
                                'zahler_zahlerbundle_payment[date][day]' : parseInt(Ext.Date.format(e.record.get('tra_date'), 'd')),
                                'zahler_zahlerbundle_payment[destinationAccount]' : e.record.get('tra_acc_id_debit'),
                                'zahler_zahlerbundle_payment[amount]' : e.record.get('tra_amount'),
                                'zahler_zahlerbundle_payment[interest]' : e.record.get('interest_amount'),
                                'zahler_zahlerbundle_payment[payLoa]' : Ext.getCmp('loans_gridpanel').getSelectionModel().getLastSelected().get('loa_id')
                            };
                            if (e.record.get('pay_id') == '') {
                                Ext.Ajax.request({
                                    url : prefijoUrl + 'payment/create',
                                    params : params,
                                    success : function(response) {
                                        var text = response.responseText;
                                        loanPaymentStore.load();
                                    },
                                    failure : function(response) {
                                        var text = response.responseText;
                                        loanPaymentStore.load();
                                    }
                                });
                            } else {
                                params._method = 'PUT';
                                Ext.Ajax.request({
                                    url : prefijoUrl + 'payment/update/' + e.record.get('pay_id'),
                                    params : params,
                                    success : function(response) {
                                        var text = response.responseText;
                                        loanPaymentStore.load();
                                    },
                                    failure : function(response) {
                                        var text = response.responseText;
                                        loanPaymentStore.load();
                                    }
                                });
                            }
                        }
                    }
                })]
            }]
        });

        var debt_payments_window = Ext.create('Ext.window.Window', {
            title : 'Debt payments',
            width : 561,
            height : 324,
            layout : 'fit',
            closeAction : 'hide',
            items : [{
                xtype : 'gridpanel',
                flex : 1,
                store : debtPaymentStore,
                columns : [{
                    dataIndex : 'dep_tra_id',
                    text : 'Transaction'
                }, {
                    xtype : 'datecolumn',
                    dataIndex : 'tra_date',
                    text : 'Date'
                }, {
                    xtype : 'numbercolumn',
                    dataIndex : 'tra_amount',
                    text : 'Amount'
                }, {
                    dataIndex : 'interest',
                    text : 'Interest transaction',
                    width : 112
                }, {
                    dataIndex : 'interest',
                    text : 'Interest'
                }],
                dockedItems : [{
                    xtype : 'pagingtoolbar',
                    store : debtPaymentStore,
                    dock : 'bottom',
                    displayInfo : true
                }]
            }]
        });

        Ext.applyIf(me, {
            items : [{
                id : 'people_grid',
                xtype : 'gridpanel',
                flex : 1,
                title : 'People',
                store : peopleStore,
                columns : [{
                    dataIndex : 'id',
                    text : 'Id'
                }, {
                    xtype : 'gridcolumn',
                    dataIndex : 'per_name',
                    text : 'Name',
                    width : 195
                }, {
                    xtype : 'numbercolumn',
                    dataIndex : 'number',
                    text : 'Loans'
                }, {
                    xtype : 'numbercolumn',
                    dataIndex : 'number',
                    text : 'Debts'
                }, {
                    xtype : 'numbercolumn',
                    dataIndex : 'number',
                    text : 'Total'
                }],
                dockedItems : [{
                    xtype : 'pagingtoolbar',
                    store : peopleStore,
                    dock : 'bottom',
                    displayInfo : true
                }],
                listeners : {
                    select : function(rowModel, record) {
                        loanStore.proxy.extraParams = {
                            person_id : record.get('id')
                        };
                        loanStore.load();

                        debtStore.proxy.extraParams = {
                            person_id : record.get('id')
                        };
                        debtStore.load();
                    }
                }
            }, {
                id : 'loans_gridpanel',
                xtype : 'gridpanel',
                flex : 1,
                title : 'Loans',
                store : loanStore,
                columns : [{
                    dataIndex : 'loa_id',
                    text : 'Id'
                }, {
                    xtype : 'datecolumn',
                    dataIndex : 'tra_date',
                    text : 'Date',
                    renderer : Ext.util.Format.dateRenderer('Y-m-d'),
                    editor : {
                        xtype : 'datefield',
                        format : 'Y-m-d',
                    }
                }, {
                    dataIndex : 'loa_description',
                    text : 'Description',
                    flex : 1,
                    editor : {
                        xtype : 'textfield'
                    }
                }, {
                    header : 'Source account',
                    dataIndex : 'tra_acc_id_credit',
                    flex : 1,
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
                    dataIndex : 'loa_interest_rate',
                    text : 'Interest rate',
                    editor : {
                        xtype : 'numberfield'
                    }
                }, {
                    xtype : 'numbercolumn',
                    dataIndex : 'tra_amount',
                    text : 'Amount',
                    editor : {
                        xtype : 'numberfield'
                    }
                }, {
                    xtype : 'numbercolumn',
                    dataIndex : 'balance',
                    text : 'Balance'
                }],
                tbar : [{
                    xtype : 'button',
                    text : 'Payments',
                    scale : 'large',
                    handler : function() {
                        // var sm = Ext.getCmp('loans_gridpanel').getSelectionModel();
                        //
                        // if (!sm.hasSelection()) {
                        // Ext.Msg.alert('Information', 'Please select a loan first');
                        // return;
                        // }

                        // var record = sm.getLastSelected();

                        loan_payments_window.show();
                    }
                }, '-'],
                dockedItems : [{
                    xtype : 'pagingtoolbar',
                    store : loanStore,
                    dock : 'bottom',
                    displayInfo : true
                }],
                listeners : {
                    select : function(sm, record) {
                        if (record.get('loa_id') != '') {
                            loanPaymentStore.proxy.extraParams = {
                                loa_id : record.get('loa_id')
                            };

                            loanPaymentStore.load();
                        }
                    }
                },
                plugins : [Ext.create('Ext.grid.plugin.RowEditing', {
                    clicksToEdit : 2,
                    listeners : {
                        edit : function(editor, e) {
                            var params = {
                                'zahler_zahlerbundle_loan[date][year]' : Ext.Date.format(e.record.get('tra_date'), 'Y'),
                                'zahler_zahlerbundle_loan[date][month]' : parseInt(Ext.Date.format(e.record.get('tra_date'), 'm')),
                                'zahler_zahlerbundle_loan[date][day]' : parseInt(Ext.Date.format(e.record.get('tra_date'), 'd')),
                                'zahler_zahlerbundle_loan[sourceAccount]' : e.record.get('tra_acc_id_credit'),
                                'zahler_zahlerbundle_loan[loaPer]' : Ext.getCmp('people_grid').getSelectionModel().getLastSelected().get('id'),
                                'zahler_zahlerbundle_loan[amount]' : e.record.get('tra_amount'),
                                'zahler_zahlerbundle_loan[loaInterestRate]' : e.record.get('loa_interest_rate'),
                                'zahler_zahlerbundle_loan[loaDescription]' : e.record.get('loa_description')
                            };
                            if (e.record.get('loa_id') == '') {
                                Ext.Ajax.request({
                                    url : prefijoUrl + 'loan/create',
                                    params : params,
                                    success : function(response) {
                                        var text = response.responseText;
                                        loanStore.load();
                                    },
                                    failure : function(response) {
                                        var text = response.responseText;
                                        loanStore.load();
                                    }
                                });
                            } else {
                                params._method = 'PUT';
                                Ext.Ajax.request({
                                    url : prefijoUrl + 'loan/update/' + e.record.get('loa_id'),
                                    params : params,
                                    success : function(response) {
                                        var text = response.responseText;
                                        loanStore.load();
                                    },
                                    failure : function(response) {
                                        var text = response.responseText;
                                        loanStore.load();
                                    }
                                });
                            }
                        }
                    }
                })]
            }, {
                xtype : 'gridpanel',
                flex : 1,
                title : 'Debts',
                store : debtStore,
                columns : [{
                    dataIndex : 'deb_id',
                    text : 'Id'
                }, {
                    dataIndex : 'tra_date',
                    text : 'Date',
                    renderer : Ext.util.Format.dateRenderer('Y-m-d'),
                    editor : {
                        xtype : 'datefield',
                        format : 'Y-m-d',
                    }
                }, {
                    dataIndex : 'deb_description',
                    text : 'Description',
                    flex : 1,
                    editor : {
                        xtype : 'textfield'
                    }
                }, {
                    header : 'Destination account',
                    dataIndex : 'tra_acc_id_debit',
                    flex : 1,
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
                    dataIndex : 'deb_interest_rate',
                    text : 'Interest rate',
                    editor : {
                        xtype : 'numberfield'
                    }
                }, {
                    xtype : 'numbercolumn',
                    dataIndex : 'tra_amount',
                    text : 'Amount',
                    editor : {
                        xtype : 'numberfield'
                    }
                }, {
                    xtype : 'numbercolumn',
                    dataIndex : 'balance',
                    text : 'Balance'
                }],
                tbar : [{
                    xtype : 'button',
                    text : 'Payments',
                    scale : 'large',
                    handler : function() {
                        // var sm = Ext.getCmp('loans_gridpanel').getSelectionModel();
                        //
                        // if (!sm.hasSelection()) {
                        // Ext.Msg.alert('Information', 'Please select a loan first');
                        // return;
                        // }

                        // var record = sm.getLastSelected();

                        debt_payments_window.show();
                    }
                }, '-'],
                dockedItems : [{
                    xtype : 'pagingtoolbar',
                    store : debtStore,
                    dock : 'bottom',
                    displayInfo : true
                }],
                listeners : {
                    select : function(rowModel, record) {
                        if (record.get('deb_id') != '') {
                            debtPaymentStore.proxy.extraParams = {
                                deb_id : record.get('deb_id')
                            };

                            debtPaymentStore.load();
                        }
                    }
                },
                plugins : [Ext.create('Ext.grid.plugin.RowEditing', {
                    clicksToEdit : 2,
                    listeners : {
                        edit : function(editor, e) {
                            var params = {
                                'zahler_zahlerbundle_debt[date][year]' : Ext.Date.format(e.record.get('tra_date'), 'Y'),
                                'zahler_zahlerbundle_debt[date][month]' : parseInt(Ext.Date.format(e.record.get('tra_date'), 'm')),
                                'zahler_zahlerbundle_debt[date][day]' : parseInt(Ext.Date.format(e.record.get('tra_date'), 'd')),
                                'zahler_zahlerbundle_debt[destinationAccount]' : e.record.get('tra_acc_id_debit'),
                                'zahler_zahlerbundle_debt[debPer]' : Ext.getCmp('people_grid').getSelectionModel().getLastSelected().get('id'),
                                'zahler_zahlerbundle_debt[amount]' : e.record.get('tra_amount'),
                                'zahler_zahlerbundle_debt[debInterestRate]' : e.record.get('deb_interest_rate'),
                                'zahler_zahlerbundle_debt[debDescription]' : e.record.get('deb_description')
                            };
                            if (e.record.get('deb_id') == '') {
                                Ext.Ajax.request({
                                    url : prefijoUrl + 'debt/create',
                                    params : params,
                                    success : function(response) {
                                        var text = response.responseText;
                                        debtStore.load();
                                    },
                                    failure : function(response) {
                                        var text = response.responseText;
                                        debtStore.load();
                                    }
                                });
                            } else {
                                params._method = 'PUT';
                                Ext.Ajax.request({
                                    url : prefijoUrl + 'debt/update/' + e.record.get('deb_id'),
                                    params : params,
                                    success : function(response) {
                                        var text = response.responseText;
                                        debtStore.load();
                                    },
                                    failure : function(response) {
                                        var text = response.responseText;
                                        debtStore.load();
                                    }
                                });
                            }
                        }
                    }
                })]
            }]
        });

        me.callParent(arguments);
    }
});
