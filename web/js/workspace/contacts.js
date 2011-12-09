var generateContactsGrid = function() {
	var datastore = new Ext.data.Store({
		proxy : new Ext.data.HttpProxy({
			url : getAbsoluteUrl('contact', 'getList'),
			method : 'POST'
		}),
		reader : new Ext.data.JsonReader({
			root : 'data',
		}, [{
			name : 'contact_id',
			type : 'string'
		}, {
			name : 'first_name',
			type : 'string'
		}, {
			name : 'last_name',
			type : 'string'
		}, {
			name : 'email',
			type : 'string'
		}, {
			name : 'address',
			type : 'string'
		}, {
			name : 'phone_number',
			type : 'string'
		}])
	});

	datastore.load();

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
				Ext.Ajax.request({
					url : getAbsoluteUrl('contact', 'create'),
					failure : function() {
						datastore.load();
					},
					success : function() {
						datastore.load();
					},
					params : {
						'contact_id' : record.get('contact_id'),
						'first_name' : record.get('first_name'),
						'last_name' : record.get('last_name'),
						email : record.get('email'),
						address : record.get('address'),
						'phone_number' : record.get('phone_number'),
					}
				});
			},
			'canceledit' : function() {
				datastore.load();
			}
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
			header : 'Id. de contacto',
			width : 110,
			dataIndex : 'contact_id'
		}, {
			header : 'Nombres',
			width : 95,
			dataIndex : 'first_name',
			editor : new Ext.form.TextField({})
		}, {
			header : 'Apellidos',
			width : 95,
			dataIndex : 'last_name',
			editor : new Ext.form.TextField({})
		}, {
			header : 'Email',
			width : 180,
			dataIndex : 'email',
			editor : new Ext.form.TextField({})
		}, {
			header : 'Dirección',
			width : 180,
			dataIndex : 'address',
			editor : new Ext.form.TextField({})
		}, {
			header : 'Teléfono',
			width : 95,
			dataIndex : 'phone_number',
			editor : new Ext.form.TextField({})
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
			layout : 'form',
			bodyStyle : 'padding-top: 10px;',
			items : [gridpanel],
			buttonAlign : 'left',
			buttons : [{
				text : 'Adicionar',
				handler : function() {
					var row = new gridpanel.store.recordType({
						'contact_id' : '',
						'first_name' : '',
						'last_name' : '',
						'email' : '',
						'address' : '',
						'phone_number' : ''
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
						var selectedContactId = record.get('contact_id');
						Ext.Ajax.request({
							url : getAbsoluteUrl('transaction', 'delete'),
							failure : function() {
								datastore.load();
							},
							success : function() {
								datastore.load();
							},
							params : {
								id : selectedContactId
							}
						});
					} else {
						alert('Select a contact first');
					}
				}
			}]
		}]
	};
}