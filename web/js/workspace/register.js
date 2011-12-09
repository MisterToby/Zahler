var generateRegister = function(accountType) {
	var datastore = new Ext.data.Store({
		proxy : new Ext.data.HttpProxy({
			url : getAbsoluteUrl('transaction', 'getListOfEntries'),
			method : 'POST'
		}),
		reader : new Ext.data.JsonReader({
			root : 'data',
		}, [{
			name : 'transaction_id',
			type : 'string'
		}, {
			name : 'date',
			type : 'date',
			dateFormat : 'd-m-Y'
		}, {
			name : 'reference',
			type : 'string'
		}, {
			name : 'description',
			type : 'string'
		}, {
			name : 'to_from_account_id',
			type : 'string'
		}, {
			name : 'debit',
			type : 'string'
		}, {
			name : 'credit',
			type : 'string'
		}, {
			name : 'balance',
			type : 'string'
		}])
	});

	var accounts_datastore = new Ext.data.Store({
		proxy : new Ext.data.HttpProxy({
			url : getAbsoluteUrl('account', 'getAccountList'),
			method : 'POST'
		}),
		baseParams : {
			account_type : accountType
		},
		reader : new Ext.data.JsonReader({
			root : 'data',
		}, [{
			name : 'account_id',
			type : 'integer'
		}, {
			name : 'account_name',
			type : 'string'
		}])
	});

	accounts_datastore.load();

	var all_accounts_datastore = new Ext.data.Store({
		proxy : new Ext.data.HttpProxy({
			url : getAbsoluteUrl('account', 'getAccountList'),
			method : 'POST'
		}),
		reader : new Ext.data.JsonReader({
			root : 'data',
		}, [{
			name : 'account_id',
			type : 'integer'
		}, {
			name : 'account_name',
			type : 'string'
		}])
	});

	all_accounts_datastore.load();

	var categorized_accounts_datastore = new Ext.data.Store({
		proxy : new Ext.data.HttpProxy({
			url : getAbsoluteUrl('account', 'getCategorizedAccountList'),
			method : 'POST'
		}),
		reader : new Ext.data.JsonReader({
			root : 'data',
		}, [{
			name : 'account_id',
			type : 'integer'
		}, {
			name : 'account_name',
			type : 'string'
		}])
	});

	categorized_accounts_datastore.load();

	var combobox = new Ext.form.ComboBox({
		fieldLabel : 'Cuenta',
		store : accounts_datastore,
		displayField : 'account_name',
		valueField : 'account_id',
		triggerAction : 'all',
		mode : 'local',
		forceSelection : true,
		listeners : {
			select : function() {
				datastore.load({
					params : {
						account_id : combobox.getValue()
					},
					callback : function() {
						gridpanel.enable();
					}
				});
			}
		}
	});

	var roweditor = new Ext.ux.grid.RowEditor({
		saveText : 'Save',
		cancelText : 'Cancel',
		errorSummary : false,
		onKey : function(f, e) {
			if(e.getKey() === e.ENTER && this.isValid()) {
				this.stopEditing(true);
				e.stopPropagation();
			}
		},
		listeners : {
			'afteredit' : function() {
				var record = gridpanel.getSelectionModel().getSelected();
				var accountId = combobox.getValue();
				var debit = record.get('debit');
				var credit = record.get('credit');
				var value = debit - credit;
				if(value < 0) {
					value = -value;
					var toAccountId = record.get('to_from_account_id');
					var fromAccountId = accountId;
				} else {
					var toAccountId = accountId;
					var fromAccountId = record.get('to_from_account_id');
				}
				var date = new Date(record.get('date'));
				Ext.Ajax.request({
					url : getAbsoluteUrl('transaction', 'create'),
					failure : function() {
						datastore.load({
							params : {
								account_id : accountId
							}
						});
					},
					success : function() {
						datastore.load({
							params : {
								account_id : accountId
							}
						});
					},
					params : {
						'transaction_id' : record.get('transaction_id'),
						date : date.format('d-m-Y'),
						reference : record.get('reference'),
						description : record.get('description'),
						'to_account_id' : toAccountId,
						'from_account_id' : fromAccountId,
						value : value
					}
				});
			},
			'canceledit' : function() {
				var accountId = combobox.getValue();
				datastore.load({
					params : {
						account_id : accountId
					}
				});
			}
		}
	});

	var gridpanel = new Ext.grid.GridPanel({
		store : datastore,
		frame : true,
		plugins : [roweditor],
		border : false,
		disabled : true,
		selModel : new Ext.grid.RowSelectionModel({
			singleSelect : true
		}),
		columns : [{
			header : 'Id. de transacción',
			width : 110,
			dataIndex : 'transaction_id'
		}, {
			xtype : 'datecolumn',
			format : 'd-m-Y',
			header : "Fecha",
			width : 90,
			dataIndex : 'date',
			renderer : function(value) {
				return value ? value.dateFormat('d-m-Y') : '';
			},
			editor : new Ext.form.DateField({
				format : 'd-m-Y',
				allowBlank : false
			})
		}, {
			header : 'Referencia',
			width : 95,
			dataIndex : 'reference',
			editor : new Ext.form.TextField({})
		}, {
			header : 'Descripción',
			width : 180,
			dataIndex : 'description',
			editor : new Ext.form.TextField({})
		}, {
			header : 'Cuenta de transferencia',
			width : 200,
			dataIndex : 'to_from_account_id',
			renderer : function(value) {
				var index = all_accounts_datastore.find('account_id', value);
				if(index != -1) {
					var record = all_accounts_datastore.getAt(index);
					return record.get('account_name');
				} else {
					return '';
				}
			},
			editor : new Ext.form.ComboBox({
				store : categorized_accounts_datastore,
				displayField : 'account_name',
				valueField : 'account_id',
				mode : 'local',
				triggerAction : 'all',
				forceSelection : true,
				allowBlank : false
			})
		}, {
			header : 'Débito',
			width : 110,
			align : 'right',
			dataIndex : 'debit',
			editor : new Ext.form.NumberField({
				allowBlank : false
			}),
			renderer : function(value) {
				return Ext.util.Format.usMoney(value);
			}
		}, {
			header : 'Crédito',
			width : 110,
			align : 'right',
			dataIndex : 'credit',
			editor : new Ext.form.NumberField({
				allowBlank : false
			}),
			renderer : function(value) {
				return Ext.util.Format.usMoney(value);
			}
		}, {
			header : 'Saldo',
			width : 110,
			align : 'right',
			dataIndex : 'balance',
			renderer : function(value) {
				return Ext.util.Format.usMoney(value);
			}
		}],
		width : '100%',
		height : 240,
		wrap : true,
		stripeRows : true,
		clicksToEdit : 1
	});

	return {
		height : 400,
		autoWidth : true,
		layout : 'form',
		items : [{
			layout : 'column',
			items : [{
				layout : 'form',
				width : 350,
				items : [combobox]
			}, {
				layout : 'form',
				bodyStyle : 'padding-left: 10px;',
				items : [{
					xtype : 'button',
					text : 'Crear',
					width : 80,
					handler : function() {
						Ext.Msg.prompt('Crear cuenta', "Digite el nombre de la cuenta", function(idButton, text) {
							if(idButton == 'ok' && text != '') {
								Ext.Ajax.request({
									url : getAbsoluteUrl('account', 'create'),
									params : {
										'account_name' : text,
										'account_type' : accountType
									},
									success : function(response) {
										if(response.responseText == 'ok') {
											accounts_datastore.load();
											all_accounts_datastore.load();
											categorized_accounts_datastore.load();
											alert('Account created');
										}
									}
								});
							}
						});
					}
				}]
			}, {
				layout : 'form',
				bodyStyle : 'padding-left: 10px;',
				items : [{
					xtype : 'button',
					text : 'Eliminar',
					width : 80,
					handler : function() {
						var accountId = combobox.getValue();
						if(accountId != '') {
							Ext.Msg.confirm('Eliminar cuenta', "¿Estás de eliminar esta cuenta?", function(idButton) {
								if(idButton == 'yes') {
									Ext.Ajax.request({
										url : getAbsoluteUrl('account', 'delete'),
										params : {
											'id' : accountId
										},
										success : function(response) {
											if(response.responseText == 'ok') {
												gridpanel.disable();
												combobox.setValue('');
												accounts_datastore.load();
												all_accounts_datastore.load();
												categorized_accounts_datastore.load();
												alert('Account deleted');
											}
										}
									});
								}
							});
						} else {
							alert('Select an account first');
						}
					}
				}]
			}]
		}, {
			layout : 'form',
			bodyStyle : 'padding-top: 10px;',
			items : [gridpanel],
			buttonAlign : 'left',
			buttons : [{
				text : 'Adicionar',
				handler : function() {
					var row = new gridpanel.store.recordType({
						'transaction_id' : '',
						'date' : '',
						'reference' : '',
						'description' : '',
						'target_account_id' : '',
						'debit' : '',
						'credit' : '',
						'balance' : ''
					});
					gridpanel.getSelectionModel().clearSelections();
					roweditor.stopEditing();
					gridpanel.store.insert(0, row);
					gridpanel.getSelectionModel().selectRow(0);
					roweditor.startEditing(0);
				}
			}, {
				text : 'Eliminar',
				handler : function() {
					if(gridpanel.getSelectionModel().hasSelection()) {
						var record = gridpanel.getSelectionModel().getSelected();
						var selectedTransactionId = record.get('transaction_id');
						var accountId = combobox.getValue();
						Ext.Ajax.request({
							url : getAbsoluteUrl('transaction', 'delete'),
							failure : function() {
								datastore.load({
									params : {
										account_id : accountId
									}
								});
							},
							success : function() {
								datastore.load({
									params : {
										account_id : accountId
									}
								});
							},
							params : {
								id : selectedTransactionId
							}
						});
					} else {
						alert('Select a transaction first');
					}
				}
			}]
		}]
	};
}