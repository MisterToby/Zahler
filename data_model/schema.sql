
-----------------------------------------------------------------------------
-- usuario
-----------------------------------------------------------------------------

DROP TABLE IF EXISTS usuario CASCADE;


CREATE TABLE usuario
(
	usu_login VARCHAR(20)  NOT NULL,
	usu_password VARCHAR(40)  NOT NULL,
	PRIMARY KEY (usu_login)
);

-----------------------------------------------------------------------------
-- cuenta
-----------------------------------------------------------------------------

DROP TABLE IF EXISTS cuenta CASCADE;

CREATE TABLE cuenta
(
	cue_id serial  NOT NULL,
	cue_nombre VARCHAR(20)  NOT NULL,
	cue_tipo INTEGER  NOT NULL,
	PRIMARY KEY (cue_id)
);

-----------------------------------------------------------------------------
-- transaccion
-----------------------------------------------------------------------------

DROP TABLE IF EXISTS transaccion CASCADE;

CREATE TABLE transaccion
(
	tra_id serial  NOT NULL,
	tra_fecha DATE  NOT NULL,
	tra_referencia VARCHAR(20)  NOT NULL,
	tra_descripcion VARCHAR(255)  NOT NULL,
	tra_valor FLOAT  NOT NULL,
	tra_cue_id_debito INTEGER  NOT NULL,
	tra_cue_id_credito INTEGER  NOT NULL,
	PRIMARY KEY (tra_id)
);

-----------------------------------------------------------------------------
-- contacto
-----------------------------------------------------------------------------

DROP TABLE IF EXISTS contacto CASCADE;

CREATE TABLE contacto
(
	con_id serial  NOT NULL,
	con_nombres VARCHAR(50)  NOT NULL,
	con_apellidos VARCHAR(50),
	con_email VARCHAR(320),
	con_direccion VARCHAR(100),
	con_telefono VARCHAR(20),
	PRIMARY KEY (con_id)
);

-----------------------------------------------------------------------------
-- prestamo
-----------------------------------------------------------------------------

DROP TABLE IF EXISTS prestamo CASCADE;

CREATE TABLE prestamo
(
	pre_id serial  NOT NULL,
	pre_con_id INTEGER  NOT NULL,
	pre_tra_id INTEGER  NOT NULL,
	pre_tasa_interes FLOAT  NOT NULL,
	PRIMARY KEY (pre_id)
);

-----------------------------------------------------------------------------
-- pago_prestamo
-----------------------------------------------------------------------------

DROP TABLE IF EXISTS pago_prestamo CASCADE;

CREATE TABLE pago_prestamo
(
	pap_id serial  NOT NULL,
	pap_pre_id INTEGER  NOT NULL,
	pap_tra_id INTEGER  NOT NULL,
	PRIMARY KEY (pap_id)
);

ALTER TABLE transaccion ADD CONSTRAINT transaccion_FK_1 FOREIGN KEY (tra_cue_id_debito) REFERENCES cuenta (cue_id);

ALTER TABLE transaccion ADD CONSTRAINT transaccion_FK_2 FOREIGN KEY (tra_cue_id_credito) REFERENCES cuenta (cue_id);

ALTER TABLE prestamo ADD CONSTRAINT prestamo_FK_1 FOREIGN KEY (pre_con_id) REFERENCES contacto (con_id);

ALTER TABLE prestamo ADD CONSTRAINT prestamo_FK_2 FOREIGN KEY (pre_tra_id) REFERENCES transaccion (tra_id);

ALTER TABLE pago_prestamo ADD CONSTRAINT pago_prestamo_FK_1 FOREIGN KEY (pap_pre_id) REFERENCES prestamo (pre_id);

ALTER TABLE pago_prestamo ADD CONSTRAINT pago_prestamo_FK_2 FOREIGN KEY (pap_tra_id) REFERENCES transaccion (tra_id);

ALTER TABLE public.usuario OWNER TO zahler;
ALTER TABLE public.contacto OWNER TO zahler;
ALTER TABLE public.cuenta OWNER TO zahler;
ALTER TABLE public.pago_prestamo OWNER TO zahler;
ALTER TABLE public.prestamo OWNER TO zahler;
ALTER TABLE public.transaccion OWNER TO zahler;
