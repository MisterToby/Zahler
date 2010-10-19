var login_title_panel = new Ext.Panel({
    frame: false,
    border: false,
    height: 110,
    html: '<font face="arial" size=36 color=#4E79B2><center>Agua Rural de Colombia</center></font><br /><font face="arial" size=6 color=#4E79B2><center>Peque&ntilde;os Prestadores</center></font>',
    width: 600,
});

var login_panel = new Ext.form.FormPanel({
    //frame: true,
    //    autoHeight: true,
    padding: 10,
    defaultType: 'textfield',
    border: false,
    bodyStyle: 'background-color: transparent;',
    x: 338,
    y: 252,
    width: 160,
    height: 200,
    hideLabel: true,
    //    labelWidth: 0,
    xtype: 'fieldset',
    layout: 'absolute',
    items: [{
        x: 10,
        y: 11,
        validateOnBlur: false,
        hideLabel: true,
        style: {
            'background-color': 'transparent',
            'background-image': 'none',
            'border-color': 'transparent',
            'color': 'gray'
        },
        emptyText: 'User name',
        anchor: '100%',
        id: 'user_name',
        name: 'user_name',
        maxLength: 20,
        minLength: 4,
        vtype: 'alphanum',
        allowBlank: false,
        enableKeyEvents: true,
        listeners: {
            specialkey: function(field, e){
                if (e.getKey() == e.ENTER) {
                    if (Ext.getCmp('usu_login').isValid() && Ext.getCmp('usu_clave').isValid()) {
                        login_autenticar();
                    }
                    else {
                        Ext.example.msg('Error', 'campos incompletos');
                    }
                }
            }
        }
    }, {
        x: 10,
        y: 66,
        hideLabel: true,
        style: {
            'background-color': 'transparent',
            'background-image': 'none',
            'border-color': 'transparent',
            'color': 'gray'
        },
        inputType: 'password',
        maxLength: 32,
        minLength: 4,
        emptyText: 'Contraseña',
        anchor: '100%',
        id: 'password',
        name: 'password',
        allowBlank: false,
        listeners: {
            specialkey: function(field, e){
                if (e.getKey() == e.ENTER) {
                    if (Ext.getCmp('user_name').isValid() && Ext.getCmp('password').isValid()) {
                        login_autenticar();
                    }
                    else {
                        Ext.example.msg('Error', 'campos incompletos');
                    }
                }
            }
        }
    }]
    //    ,
    //    buttons: [{
    //        text: 'Ingresar',
    //        id: 'BguardarEjemplar',
    //        //iconCls:'crear16',
    //        iconCls: 'login',
    //        handler: function(){
    //            if (Ext.getCmp('usu_login').isValid() && Ext.getCmp('usu_clave').isValid()) {
    //                login_autenticar();
    //            }
    //            else {
    //                Ext.example.msg('Error', 'campos incompletos');
    //            }
    //        }
    //    }]
});

var form_login = new Ext.Panel({
    renderTo: 'form_authentication_div',
    //    frame: true,
    padding: 5,
    layout: 'absolute',
    //    title: 'Welcome to Zahler',
    bodyStyle: 'background-image: url(' + webUrlFolder + 'images/authentication/authentication_form_background.png); background-color: transparent;',
    border: false,
    width: 800,
    height: 600,
    items: [/*login_title_panel,*/login_panel    /*
     html:'<a href="'+webUrlFolder+'manual-arc/main.html"> Manual</a>'
     */
    ]
});


function login_autenticar(){
    Ext.Ajax.request({
        waitMsg: 'Espere por favor',
        url: getAbsoluteUrl('authentication', 'authenticate'),
        params: {
            user_name: Ext.getCmp('user_name').getValue(),
            password: Ext.getCmp('password').getValue()
        },
        success: function(response, action){
            obj = Ext.util.JSON.decode(response.responseText);
            if (obj.success) {
                window.location = getAbsoluteUrl('workspace', 'index');
            }
            else {
                Ext.Msg.alert('Error', obj.msg);
            }
        },
        failure: function(response){
            Ext.Msg.alert('Error', 'It was not possible to establish a connection to the server. Try again later');
        }
    });
}

