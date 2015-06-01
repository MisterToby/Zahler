Ext.Loader.setConfig({
    enabled : true
});

Ext.Loader.setPath('LoansDebtsApp.view.LoansDebts', resourcesPath + 'js/LoansDebts/app/view/LoansDebts.js');

Ext.application({

    requires : ['LoansDebtsApp.view.LoansDebts'],
    views : ['LoansDebts'],
    name : 'LoansDebtsApp',

    launch : function() {
        var panel = Ext.create('LoansDebtsApp.view.LoansDebts');

        Ext.create('Ext.container.Viewport', {
            layout : 'border',
            items : [panel]
        });
    }
});
