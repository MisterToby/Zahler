var generateLoansGrid = function() {
	var datastore = new Ext.data.Store({
		proxy : new Ext.data.HttpProxy({
			url : getAbsoluteUrl('prestamo', 'consultarLista'),
			method : 'POST'
		}),
		reader : new Ext.data.JsonReader({
			root : 'data',
		}, [{
			name : 'loan_id',
			type : 'string'
		}, {
			name : 'contact_id',
			type : 'string'
		}, {
			name : 'date',
			type : 'date',
			dateFormat : 'd-m-Y'
		}, {
			name : 'transaction_id',
			type : 'string'
		}, {
			name : 'interest_rate',
			type : 'float'
		}, {
			name : 'amount',
			type : 'float'
		}, {
			name : 'source_account_id',
			type : 'string'
		}, {
			name : 'loans_account_id',
			type : 'string'
		}])
	});

	var roweditor = new Ext.ux.grid.RowEditor({
		saveText : 'Guardar',
		cancelText : 'Cancelar',
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
				Ext.Ajax.request({
					url : getAbsoluteUrl('prestamo', 'crear'),
					failure : function() {
						datastore.load();
					},
					success : function() {
						datastore.load();
					},
					params : {
						'loan_id' : record.get('loan_id'),
						'contact_id' : record.get('contact_id'),
						'date' : record.get('date'),
						'interest_rate' : record.get('interest_rate'),
						'amount' : record.get('amount'),
						'source_account_id' : record.get('source_account_id'),
						'loans_account_id' : record.get('loans_account_id')
					}
				});
			},
			'canceledit' : function() {
				datastore.load();
			}
		}
	});

	var contacts_datastore = new Ext.data.Store({
		proxy : new Ext.data.HttpProxy({
			url : getAbsoluteUrl('contacto', 'consultarListaNombreCompleto'),
			method : 'POST'
		}),
		reader : new Ext.data.JsonReader({
			root : 'data',
		}, [{
			name : 'contact_id',
			type : 'integer'
		}, {
			name : 'contact_name',
			type : 'string'
		}])
	});

	contacts_datastore.load();

	var assets_accounts_datastore = new Ext.data.Store({
		proxy : new Ext.data.HttpProxy({
			url : getAbsoluteUrl('cuenta', 'consultarListaCuentas'),
			method : 'POST'
		}),
		reader : new Ext.data.JsonReader({
			root : 'data',
		}, [{
			name : 'id',
			type : 'integer'
		}, {
			name : 'nombre',
			type : 'string'
		}])
	});

	assets_accounts_datastore.load({
		params : {
			'account_type' : ASSETS_ACCOUNT
		},
		callback : function() {
			datastore.load();
		}
	});

	var gridpanel = new Ext.grid.GridPanel({
		store : datastore,
		frame : true,
		plugins : [roweditor],
		border : false,
		selModel : new Ext.grid.RowSelectionModel({
			singleSelect : true
		}),
		columns : [{
			header : 'Id. de préstamo',
			width : 110,
			dataIndex : 'loan_id'
		}, {
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
			header : 'Prestatario',
			width : 220,
			dataIndex : 'contact_id',
			renderer : function(value) {
				var index = contacts_datastore.find('contact_id', value);
				if(index != -1) {
					var record = contacts_datastore.getAt(index);
					return record.get('contact_name');
				} else {
					return '';
				}
			},
			editor : new Ext.form.ComboBox({
				store : contacts_datastore,
				displayField : 'contact_name',
				valueField : 'contact_id',
				mode : 'local',
				triggerAction : 'all',
				forceSelection : true,
				allowBlank : false
			})
		}, {
			header : 'Tasa E.A (%)',
			width : 95,
			align : 'right',
			dataIndex : 'interest_rate',
			editor : new Ext.form.NumberField({
				allowBlank : false
			})
		}, {
			header : 'Valor',
			width : 180,
			align : 'right',
			dataIndex : 'amount',
			editor : new Ext.form.NumberField({
				allowBlank : false
			}),
			renderer : function(value) {
				return Ext.util.Format.usMoney(value);
			}
		}, {
			header : 'Cuenta de origen',
			width : 220,
			dataIndex : 'source_account_id',
			renderer : function(value) {
				var index = assets_accounts_datastore.find('account_id', value);
				if(index != -1) {
					var record = assets_accounts_datastore.getAt(index);
					return record.get('account_name');
				} else {
					return '';
				}
			},
			editor : new Ext.form.ComboBox({
				store : assets_accounts_datastore,
				displayField : 'nombre',
				valueField : 'id',
				mode : 'local',
				triggerAction : 'all',
				forceSelection : true,
				allowBlank : false
			})
		}, {
			header : 'Cuenta de destino',
			width : 220,
			dataIndex : 'loans_account_id',
			renderer : function(value) {
				var index = assets_accounts_datastore.find('account_id', value);
				if(index != -1) {
					var record = assets_accounts_datastore.getAt(index);
					return record.get('account_name');
				} else {
					return '';
				}
			},
			editor : new Ext.form.ComboBox({
				store : assets_accounts_datastore,
				displayField : 'nombre',
				valueField : 'id',
				mode : 'local',
				triggerAction : 'all',
				forceSelection : true,
				allowBlank : false
			})
		}],
		width : '100%',
		height : 240,
		wrap : true,
		stripeRows : true,
		clicksToEdit : 2
	});

	var payments_made_datastore = new Ext.data.Store({
		proxy : new Ext.data.HttpProxy({
			url : getAbsoluteUrl('prestamo', 'consultarListaPagos'),
			method : 'POST'
		}),
		reader : new Ext.data.JsonReader({
			root : 'data',
		}, [{
			name : 'payment_id',
			type : 'integer'
		}, {
			name : 'date',
			type : 'date',
			dateFormat : 'd-m-Y'
		}, {
			name : 'amount',
			type : 'string'
		}, {
			name : 'payments_account_id',
			type : 'string'
		}])
	});

	var payments_made_roweditor = new Ext.ux.grid.RowEditor({
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
				if(gridpanel.getSelectionModel().hasSelection()) {
					var loanRecord = gridpanel.getSelectionModel().getSelected();
					var record = payments_made_grid.getSelectionModel().getSelected();
					Ext.Ajax.request({
						url : getAbsoluteUrl('prestamo', 'registrarPago'),
						failure : function() {
							updateDetailsData();
						},
						success : function() {
							updateDetailsData();
						},
						params : {
							'payment_id' : record.get('payment_id'),
							'loan_id' : loanRecord.get('loan_id'),
							'date' : record.get('date'),
							'amount' : record.get('amount'),
							'payments_account_id' : record.get('payments_account_id')
						}
					});
				}
			},
			'canceledit' : function() {
				var record = gridpanel.getSelectionModel().getSelected();
				payments_made_datastore.setBaseParam('loan_id', record.get('loan_id'));
			}
		}
	});

	var payments_made_grid = new Ext.grid.GridPanel({
		store : payments_made_datastore,
		frame : true,
		plugins : [payments_made_roweditor],
		border : false,
		selModel : new Ext.grid.RowSelectionModel({
			singleSelect : true
		}),
		columns : [{
			header : 'Id. de pago',
			width : 110,
			dataIndex : 'payment_id'
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
			header : 'Valor del pago',
			width : 180,
			align : 'right',
			dataIndex : 'amount',
			editor : new Ext.form.NumberField({
				allowBlank : false
			}),
			renderer : function(value) {
				return Ext.util.Format.usMoney(value);
			}
		}, {
			header : 'Cuenta de destino',
			width : 220,
			dataIndex : 'payments_account_id',
			renderer : function(value) {
				var index = assets_accounts_datastore.find('account_id', value);
				if(index != -1) {
					var record = assets_accounts_datastore.getAt(index);
					return record.get('account_name');
				} else {
					return '';
				}
			},
			editor : new Ext.form.ComboBox({
				store : assets_accounts_datastore,
				displayField : 'account_name',
				valueField : 'account_id',
				mode : 'local',
				triggerAction : 'all',
				forceSelection : true,
				allowBlank : false
			})
		}],
		width : '100%',
		height : 240,
		wrap : true,
		stripeRows : true,
		clicksToEdit : 1,
		tbar : {
			items : [{
				xtype : 'label',
				text : 'Saldo:'
			}, ' ', {
				id : 'current_balance',
				xtype : 'textfield',
				readOnly : true,
				style : {
					'text-align' : 'right'
				}
			}, ' ', {
				xtype : 'label',
				text : 'Intereses:'
			}, ' ', {
				id : 'interests',
				xtype : 'textfield',
				readOnly : true,
				style : {
					'text-align' : 'right'
				}
			}, ' ', {
				xtype : 'label',
				text : 'Pago total:'
			}, ' ', {
				id : 'full_payment',
				xtype : 'textfield',
				fieldLabel : 'Full payment',
				readOnly : true,
				style : {
					'text-align' : 'right'
				}
			}]
		}
	});

	var details_floating_window = new Ext.Window({
		applyTo : 'floating_window',
		layout : 'fit',
		width : 650,
		height : 300,
		closeAction : 'hide',
		plain : true,
		title : 'Procesar pagos',
		items : [payments_made_grid],
		buttons : [{
			text : 'Adicionar',
			handler : function() {
				var row = new payments_made_grid.store.recordType({
					'payment_id' : '',
					'date' : '',
					'amount' : '',
					'payments_account_id' : ''
				});
				payments_made_grid.getSelectionModel().clearSelections();
				payments_made_roweditor.stopEditing();
				payments_made_grid.store.insert(0, row);
				payments_made_grid.getSelectionModel().selectRow(0);
				payments_made_roweditor.startEditing(0);
			}
		}, {
			text : 'Eliminar',
			handler : function() {
				if(payments_made_grid.getSelectionModel().hasSelection()) {
					var record = payments_made_grid.getSelectionModel().getSelected();
					var selectedPaymentId = record.get('payment_id');
					Ext.Ajax.request({
						url : getAbsoluteUrl('prestamo', 'eliminarPago'),
						failure : function() {
							updateDetailsData();
						},
						success : function(result) {
							updateDetailsData();
							if(result.responseText == 'Ok') {

							} else {
								alert(result.responseText);
							}
						},
						params : {
							id : selectedPaymentId
						}
					});
				} else {
					alert('Select a payment first');
				}
			}
		}, {
			text : 'Ok',
			handler : function() {
				details_floating_window.hide();
			}
		}],
		listeners : {
			hide : function() {
				Ext.getBody().unmask();
			}
		}
	});

	var details_datastore = new Ext.data.Store({
		proxy : new Ext.data.HttpProxy({
			url : getAbsoluteUrl('prestamo', 'consultarDetallesPrestamo'),
			method : 'POST'
		}),
		reader : new Ext.data.JsonReader({
			root : 'data',
		}, [{
			name : 'current_balance'
		}, {
			name : 'interests'
		}, {
			name : 'full_payment'
		}])
	});

	var updateDetailsData = function() {
		payments_made_datastore.load();
		details_datastore.load({
			callback : function() {
				var detailsRecord = details_datastore.getAt(0);
				var currentBalanceTextfield = Ext.getCmp('current_balance');
				currentBalanceTextfield.setValue('$ ' + detailsRecord.get('current_balance'));
				var interestsTextfield = Ext.getCmp('interests');
				interestsTextfield.setValue('$ ' + detailsRecord.get('interests'));
				var fullPaymentTextfield = Ext.getCmp('full_payment');
				fullPaymentTextfield.setValue('$ ' + detailsRecord.get('full_payment'));
			}
		});
	}
	return {
		height : 400,
		autoWidth : true,
		layout : 'form',
		items : [{
			layout : 'form',
			bodyStyle : 'padding-top: 10px;',
			items : [gridpanel],
			buttonAlign : 'left',
			buttons : [{
				text : 'Adicionar',
				handler : function() {
					var row = new gridpanel.store.recordType({
						'loan_id' : '',
						'contact_id' : '',
						'date' : '',
						'transaction_id' : '',
						'interest_rate' : '',
						'amount' : '',
						'source_account_id' : '',
						'loans_account_id' : ''
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
						var selectedLoanId = record.get('loan_id');
						Ext.Ajax.request({
							url : getAbsoluteUrl('prestamo', 'eliminar'),
							failure : function() {
								datastore.load();
							},
							success : function(result) {
								datastore.load();
							},
							params : {
								id : selectedLoanId
							}
						});
					} else {
						alert('Select a loan first');
					}
				}
			}, {
				text : 'Procesar pagos',
				handler : function() {
					if(gridpanel.getSelectionModel().hasSelection()) {
						var record = gridpanel.getSelectionModel().getSelected();
						Ext.getBody().mask();
						details_floating_window.show();
						payments_made_datastore.setBaseParam('loan_id', record.get('loan_id'));
						details_datastore.setBaseParam('loan_id', record.get('loan_id'));

						updateDetailsData();
					}
				}
			}]
		}],
		listeners : {
			activate : function() {
			}
		}
	};
}