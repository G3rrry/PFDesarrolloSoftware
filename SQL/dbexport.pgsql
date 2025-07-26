--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: causadebaja; Type: TABLE; Schema: public; Owner: root; Tablespace: 
--

CREATE TABLE causadebaja (
    id_sap integer NOT NULL,
    causabaja character varying(255)
);


ALTER TABLE public.causadebaja OWNER TO root;

--
-- Name: certificado; Type: TABLE; Schema: public; Owner: root; Tablespace: 
--

CREATE TABLE certificado (
    numerocertificado integer NOT NULL,
    numerolote integer
);


ALTER TABLE public.certificado OWNER TO root;

--
-- Name: certificado_numerocertificado_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE certificado_numerocertificado_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.certificado_numerocertificado_seq OWNER TO root;

--
-- Name: certificado_numerocertificado_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE certificado_numerocertificado_seq OWNED BY certificado.numerocertificado;


--
-- Name: clientes; Type: TABLE; Schema: public; Owner: root; Tablespace: 
--

CREATE TABLE clientes (
    id_sap integer NOT NULL,
    rfc character varying(13),
    nombrecliente character varying(70),
    domiciliofiscal character varying(255),
    requierecertificado boolean,
    estadocliente boolean,
    correo character varying(255),
    telefono character varying(10),
    nombrecontacto character varying(70),
    correocontacto character varying(255),
    telefonocontacto character varying(10)
);


ALTER TABLE public.clientes OWNER TO root;

--
-- Name: domicilio; Type: TABLE; Schema: public; Owner: root; Tablespace: 
--

CREATE TABLE domicilio (
    iddomicilio integer NOT NULL,
    id_sap integer,
    domicilio character varying(255)
);


ALTER TABLE public.domicilio OWNER TO root;

--
-- Name: domicilio_iddomicilio_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE domicilio_iddomicilio_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.domicilio_iddomicilio_seq OWNER TO root;

--
-- Name: domicilio_iddomicilio_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE domicilio_iddomicilio_seq OWNED BY domicilio.iddomicilio;


--
-- Name: equipolaboratorio; Type: TABLE; Schema: public; Owner: root; Tablespace: 
--

CREATE TABLE equipolaboratorio (
    claveequipo integer NOT NULL,
    marca character varying(255),
    modelo character varying(255),
    serie character varying(255),
    descripcionlarga character varying(200),
    descripcioncorta character varying(50),
    claveproveedor integer,
    fechaadquisicion date,
    garantia character varying(100),
    vigenciagarantia character varying(100),
    ubicacion character varying(255),
    responsable character varying(255),
    estado character varying(255),
    tipogarantia character varying(255)
);


ALTER TABLE public.equipolaboratorio OWNER TO root;

--
-- Name: equipolaboratorio_claveequipo_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE equipolaboratorio_claveequipo_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.equipolaboratorio_claveequipo_seq OWNER TO root;

--
-- Name: equipolaboratorio_claveequipo_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE equipolaboratorio_claveequipo_seq OWNED BY equipolaboratorio.claveequipo;


--
-- Name: fechadeanalisis; Type: TABLE; Schema: public; Owner: root; Tablespace: 
--

CREATE TABLE fechadeanalisis (
    numerocertificado integer NOT NULL,
    fechaanalisis date,
    secuenciainspeccion character varying(2)
);


ALTER TABLE public.fechadeanalisis OWNER TO root;

--
-- Name: lotes; Type: TABLE; Schema: public; Owner: root; Tablespace: 
--

CREATE TABLE lotes (
    numerolote integer NOT NULL,
    fechacaducidad date,
    fechaproduccion date,
    cantidadlote integer
);


ALTER TABLE public.lotes OWNER TO root;

--
-- Name: lotes_numerolote_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE lotes_numerolote_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.lotes_numerolote_seq OWNER TO root;

--
-- Name: lotes_numerolote_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE lotes_numerolote_seq OWNED BY lotes.numerolote;


--
-- Name: orden; Type: TABLE; Schema: public; Owner: root; Tablespace: 
--

CREATE TABLE orden (
    numeroorden integer NOT NULL,
    numerocertificado integer NOT NULL,
    cantidadlote integer,
    path character varying(255)
);


ALTER TABLE public.orden OWNER TO root;

--
-- Name: parametros; Type: TABLE; Schema: public; Owner: root; Tablespace: 
--

CREATE TABLE parametros (
    idparametro integer NOT NULL,
    nombreparametro character varying(255),
    descripcion character varying(255),
    minint double precision,
    maxint double precision
);


ALTER TABLE public.parametros OWNER TO root;

--
-- Name: pedido; Type: TABLE; Schema: public; Owner: root; Tablespace: 
--

CREATE TABLE pedido (
    numeroorden integer NOT NULL,
    iddomicilio integer,
    cantidadtotal integer,
    numerofactura integer,
    fechaenvio date
);


ALTER TABLE public.pedido OWNER TO root;

--
-- Name: pedido_numeroorden_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE pedido_numeroorden_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.pedido_numeroorden_seq OWNER TO root;

--
-- Name: pedido_numeroorden_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE pedido_numeroorden_seq OWNED BY pedido.numeroorden;


--
-- Name: proveedores; Type: TABLE; Schema: public; Owner: root; Tablespace: 
--

CREATE TABLE proveedores (
    claveproveedor integer NOT NULL,
    proveedor character varying(255)
);


ALTER TABLE public.proveedores OWNER TO root;

--
-- Name: resultados; Type: TABLE; Schema: public; Owner: root; Tablespace: 
--

CREATE TABLE resultados (
    resultado double precision,
    numerocertificado integer,
    idparametro integer,
    claveequipo integer
);


ALTER TABLE public.resultados OWNER TO root;

--
-- Name: usuarios; Type: TABLE; Schema: public; Owner: root; Tablespace: 
--

CREATE TABLE usuarios (
    id integer NOT NULL,
    nombres character varying(100),
    apellidos character varying(100),
    correo character varying(100) NOT NULL,
    contrasena character varying(255) NOT NULL,
    rol integer,
    CONSTRAINT usuarios_rol_check CHECK ((rol = ANY (ARRAY[1, 2, 3])))
);


ALTER TABLE public.usuarios OWNER TO root;

--
-- Name: usuarios_id_seq; Type: SEQUENCE; Schema: public; Owner: root
--

CREATE SEQUENCE usuarios_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.usuarios_id_seq OWNER TO root;

--
-- Name: usuarios_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: root
--

ALTER SEQUENCE usuarios_id_seq OWNED BY usuarios.id;


--
-- Name: valoresdereferencia; Type: TABLE; Schema: public; Owner: root; Tablespace: 
--

CREATE TABLE valoresdereferencia (
    id_sap integer NOT NULL,
    idparametro integer NOT NULL,
    min double precision,
    max double precision
);


ALTER TABLE public.valoresdereferencia OWNER TO root;

--
-- Name: numerocertificado; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY certificado ALTER COLUMN numerocertificado SET DEFAULT nextval('certificado_numerocertificado_seq'::regclass);


--
-- Name: iddomicilio; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY domicilio ALTER COLUMN iddomicilio SET DEFAULT nextval('domicilio_iddomicilio_seq'::regclass);


--
-- Name: claveequipo; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY equipolaboratorio ALTER COLUMN claveequipo SET DEFAULT nextval('equipolaboratorio_claveequipo_seq'::regclass);


--
-- Name: numerolote; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY lotes ALTER COLUMN numerolote SET DEFAULT nextval('lotes_numerolote_seq'::regclass);


--
-- Name: numeroorden; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY pedido ALTER COLUMN numeroorden SET DEFAULT nextval('pedido_numeroorden_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: root
--

ALTER TABLE ONLY usuarios ALTER COLUMN id SET DEFAULT nextval('usuarios_id_seq'::regclass);


--
-- Data for Name: causadebaja; Type: TABLE DATA; Schema: public; Owner: root
--

COPY causadebaja (id_sap, causabaja) FROM stdin;
1002	Cierre del negocio
\.


--
-- Data for Name: certificado; Type: TABLE DATA; Schema: public; Owner: root
--

COPY certificado (numerocertificado, numerolote) FROM stdin;
1	2
2	6
3	6
4	4
5	6
6	2
7	2
8	2
9	2
10	6
11	5
12	1
\.


--
-- Name: certificado_numerocertificado_seq; Type: SEQUENCE SET; Schema: public; Owner: root
--

SELECT pg_catalog.setval('certificado_numerocertificado_seq', 12, true);


--
-- Data for Name: clientes; Type: TABLE DATA; Schema: public; Owner: root
--

COPY clientes (id_sap, rfc, nombrecliente, domiciliofiscal, requierecertificado, estadocliente, correo, telefono, nombrecontacto, correocontacto, telefonocontacto) FROM stdin;
1001	ABC123456DEF	Panadería El Buen Gusto	Av. Fiscal #456, Ciudad de México	t	t	info@buen-gusto.com	5551234567	Jorge Martínez	jorge@buen-gusto.com	5559876543
1002	GHI789012JKL	Dulcería La Delicia	Calle Fiscal #012, Monterrey	t	f	ventas@ladeli.com.mx	8187654321	María López	maria@ladeli.com.mx	8185554444
10010	CASJ030315	Juan	Av Yucatan	f	t	solracza.jr@gmail.com	5551567877	Juan Dios	juan.carranza@anahuac.mx	5553438741
123412	1323qwdqwd	Lucas	Jajaja	f	t	ll@gg.com	1231212311	Locas	fu@rfu.com	5432241112
123	rfq	Joe Biden	qe	f	t	joebiden@testemail.com	2675367821	Juanquis	juanquis@rambler.ru	9183892132
878297	VECJ880326AB3	Joe Biden	White House	f	t	joebiden@testemail.com	5527613512	Juanquis	juanquis@rambler.ru	5578216378
111111	CASJ030315222	Jony	Av Yucatan	f	t	JonyB@gmail.com	5551567877	Beltran	juan.carranza@anahuac.mx	5553438741
190020	CASJ030315222	Jony	holi S.A de C.V. Av Yucatan, 206, 304. 53120, Roma.	f	t	JonyB@gmail.com	5551567877	Juan Dios	juan.carranza@anahuac.mx	5553438741
190022	CASJ030315222	Jony	holi S.A de C.V. Av Yucatan, 206, 304. 53120, Roma.	f	t	JonyB@gmail.com	5551567877	Juan Dios	juan.carranza@anahuac.mx	5553438741
190023	CASJ030315222	Jony	holi S.A de C.V. Av Yucatan, 206, 304. 53120, Roma.	f	t	JonyB@gmail.com	5551567877	Juan Dios	juan.carranza@anahuac.mx	5553438741
109238	MELO010101010	Melo	Melo Association. Afganistan, 666, 001. 60700, Saudi.	f	t	melomano@gmal.com	5567676776	Melo	melomelo@gmail.com	5567676789
232323	CASJ030315222	mane	Holi. Av Yucatan, 666, 001. 60700, Roma.	f	t	melomano@gmal.com	5567676776	Melo	melomelo@gmail.com	5567676789
384283	CACX7605101P8	Juanquis 	Lloron. Lloron, 1, 2. 64346, Womp.	f	t	todotonoto@juanquis	7482342378	Tonoto	tonoto@rambler.ru	7823678263
384223	CACX7605101P8	Juanquis 	Lloron. Lloron, 1, 2. 64346, Womp.	f	t	todotonoto@juanquis	7482342378	Tonoto	tonoto@rambler.ru	7823678263
\.


--
-- Data for Name: domicilio; Type: TABLE DATA; Schema: public; Owner: root
--

COPY domicilio (iddomicilio, id_sap, domicilio) FROM stdin;
1	190022	Paseo, 707, . 53120, Altena.
2	190023	Paseo, 707, . 53120, Altena.
3	109238	Ajusco, 209, 002. 30901, Lejos.
4	232323	Paseo, 50, 002. 30901, Lejos.
5	384283	Error, 63, 1. 72863, Teca.
6	384283	Error, 63, 1. 72863, Teca.
7	384223	Error, 63, 1. 72863, Teca.
\.


--
-- Name: domicilio_iddomicilio_seq; Type: SEQUENCE SET; Schema: public; Owner: root
--

SELECT pg_catalog.setval('domicilio_iddomicilio_seq', 7, true);


--
-- Data for Name: equipolaboratorio; Type: TABLE DATA; Schema: public; Owner: root
--

COPY equipolaboratorio (claveequipo, marca, modelo, serie, descripcionlarga, descripcioncorta, claveproveedor, fechaadquisicion, garantia, vigenciagarantia, ubicacion, responsable, estado, tipogarantia) FROM stdin;
1	sasas	asasas	asas	sasasas	asas	1	2024-04-05	sasas	2024-04-16	asasas	asasas	activo	garantia_completa
2	prueba	prueba	sasa	asasasas	asasa	2	2024-04-10	asassas	2024-05-10	lab 2	asasasa	reparacion	garantia_parcial
4	Marca1	Modelo1	serie1	desc largaaaaa	desc corta	2	2024-05-24	2	2025-05-21	CDMX	JC	activo	garantia_completa
6	juanqui	juan	qw	holiwipiwi	hola	1	2024-05-22	aaa	2024-05-23	yo	Melo	activo	garantia_parcial
7	Marca1	Modelo1	serie1	desc largaaaaa	desc corta	2	2024-05-24	2	2025-05-21	CDMX	JC	activo	garantia_completa
5	Marca1	Modelo1	serie1	desc largaaaaa	desc corta	2	2024-05-24	2	2025-05-21	CDMX	JC	activo	garantia_completa
3	Joseph	Joseph	serie1	desc largaaaaa	desc corta	2	2024-05-24	2	2025-05-21	CDMX	JC	activo	garantia_completa
\.


--
-- Name: equipolaboratorio_claveequipo_seq; Type: SEQUENCE SET; Schema: public; Owner: root
--

SELECT pg_catalog.setval('equipolaboratorio_claveequipo_seq', 7, true);


--
-- Data for Name: fechadeanalisis; Type: TABLE DATA; Schema: public; Owner: root
--

COPY fechadeanalisis (numerocertificado, fechaanalisis, secuenciainspeccion) FROM stdin;
1	2023-01-02	A
4	2023-01-03	A
2	2023-01-04	A
3	2022-02-10	B
\.


--
-- Data for Name: lotes; Type: TABLE DATA; Schema: public; Owner: root
--

COPY lotes (numerolote, fechacaducidad, fechaproduccion, cantidadlote) FROM stdin;
1	2024-05-08	2024-04-09	89
2	2024-05-04	2024-04-11	20
3	2024-05-16	2024-05-08	33
4	2024-05-23	2024-05-08	22
5	2024-05-03	2024-05-02	0
6	2024-05-21	2024-05-07	8
\.


--
-- Name: lotes_numerolote_seq; Type: SEQUENCE SET; Schema: public; Owner: root
--

SELECT pg_catalog.setval('lotes_numerolote_seq', 6, true);


--
-- Data for Name: orden; Type: TABLE DATA; Schema: public; Owner: root
--

COPY orden (numeroorden, numerocertificado, cantidadlote, path) FROM stdin;
1	2	3	path/de/prueba
2	3	5	path/de/prueba
12	5	2	\N
12	6	3	\N
13	7	4	\N
14	8	4	\N
15	9	20	\N
15	10	8	\N
15	11	0	\N
15	12	2	\N
\.


--
-- Data for Name: parametros; Type: TABLE DATA; Schema: public; Owner: root
--

COPY parametros (idparametro, nombreparametro, descripcion, minint, maxint) FROM stdin;
1	tenacidad	...	50	55
2	extensibilidad	...	110	120
3	fuerza panadera	...	180	250
4	relacion de la curva	...	0.400000000000000022	0.599999999999999978
5	absorcion del agua	...	0.550000000000000044	0.650000000000000022
6	tiempo de desarrollo de la masa	...	180	300
7	estabilidad	...	300	600
8	grado de reblandecimiento	...	40	90
\.


--
-- Data for Name: pedido; Type: TABLE DATA; Schema: public; Owner: root
--

COPY pedido (numeroorden, iddomicilio, cantidadtotal, numerofactura, fechaenvio) FROM stdin;
1	4	3	7	2015-12-17
2	4	5	3	2013-12-17
3	4	1	123	2024-06-03
4	4	1	123	2024-06-03
5	4	1	123	2024-06-03
6	4	1	123	2024-06-03
7	4	1	123	2024-06-03
8	4	1	1234	2024-06-05
9	4	1	1234	2024-06-05
10	4	1	1234	2024-06-05
11	4	2	4	2024-06-08
12	4	2	312	2024-06-05
13	4	4	5	2024-06-06
14	4	4	5	2024-06-06
15	4	30	666	2024-05-10
\.


--
-- Name: pedido_numeroorden_seq; Type: SEQUENCE SET; Schema: public; Owner: root
--

SELECT pg_catalog.setval('pedido_numeroorden_seq', 15, true);


--
-- Data for Name: proveedores; Type: TABLE DATA; Schema: public; Owner: root
--

COPY proveedores (claveproveedor, proveedor) FROM stdin;
1	KPM Analytics
2	Anton Paar
\.


--
-- Data for Name: resultados; Type: TABLE DATA; Schema: public; Owner: root
--

COPY resultados (resultado, numerocertificado, idparametro, claveequipo) FROM stdin;
20	1	1	1
2	1	1	1
10	1	1	1
1	4	1	1
21	2	1	1
0	3	1	1
\.


--
-- Data for Name: usuarios; Type: TABLE DATA; Schema: public; Owner: root
--

COPY usuarios (id, nombres, apellidos, correo, contrasena, rol) FROM stdin;
1	Alejandra	González	alejandra.gonzalez@ejemplo.com	hashDeContraseña	1
2	Roberto	Martinez	roberto.martinez@ejemplo.com	hashDeContraseña	2
3	Sofía	Castro	sofia.castro@ejemplo.com	hashDeContraseña	3
4	Gerardo	Gomez Gomez	gg@g.com	hashDeContraseña	1
8	Otro	Usuario	otro@ejemplo.com	hashDeContraseña	2
9	Miguel	Losurdo	mmlos@ejemplo.com	hashDeContraseña	3
10	Lucas	Rodriguez	lr@ejemplo.com	hashDeContraseña	3
11	Gerardo	Losurdo	gerlos@ejemplo.com	hashDeContraseña	3
12	Roco	Jojo	rojo@ejemplo.com	hashDeContraseña	2
13	Lucas	Gomez Gomez	ui@ejemplo	hashDeContraseña	3
14	Roco	Rodriguez	r@ej.com	hashDeContraseña	2
\.


--
-- Name: usuarios_id_seq; Type: SEQUENCE SET; Schema: public; Owner: root
--

SELECT pg_catalog.setval('usuarios_id_seq', 14, true);


--
-- Data for Name: valoresdereferencia; Type: TABLE DATA; Schema: public; Owner: root
--

COPY valoresdereferencia (id_sap, idparametro, min, max) FROM stdin;
10010	1	1	10
10010	2	1	10
10010	3	1	10
10010	7	2	4
10010	8	2	20
123412	1	19	56
123412	2	1	128
123412	3	18	19
123412	4	10	12
123412	7	1	67
123412	8	19	56
109238	1	1	7
109238	2	4	6
232323	1	9	89
384223	1	65	45
384223	2	2	65
384223	3	63	72
384223	4	12	32
384223	5	32	42
384223	6	4	34
384223	7	43	44
384223	8	66	65
\.


--
-- Name: causadebaja_pkey; Type: CONSTRAINT; Schema: public; Owner: root; Tablespace: 
--

ALTER TABLE ONLY causadebaja
    ADD CONSTRAINT causadebaja_pkey PRIMARY KEY (id_sap);


--
-- Name: certificado_pkey; Type: CONSTRAINT; Schema: public; Owner: root; Tablespace: 
--

ALTER TABLE ONLY certificado
    ADD CONSTRAINT certificado_pkey PRIMARY KEY (numerocertificado);


--
-- Name: clientes_pkey; Type: CONSTRAINT; Schema: public; Owner: root; Tablespace: 
--

ALTER TABLE ONLY clientes
    ADD CONSTRAINT clientes_pkey PRIMARY KEY (id_sap);


--
-- Name: domicilio_pkey; Type: CONSTRAINT; Schema: public; Owner: root; Tablespace: 
--

ALTER TABLE ONLY domicilio
    ADD CONSTRAINT domicilio_pkey PRIMARY KEY (iddomicilio);


--
-- Name: equipolaboratorio_pkey; Type: CONSTRAINT; Schema: public; Owner: root; Tablespace: 
--

ALTER TABLE ONLY equipolaboratorio
    ADD CONSTRAINT equipolaboratorio_pkey PRIMARY KEY (claveequipo);


--
-- Name: fechadeanalisis_pkey; Type: CONSTRAINT; Schema: public; Owner: root; Tablespace: 
--

ALTER TABLE ONLY fechadeanalisis
    ADD CONSTRAINT fechadeanalisis_pkey PRIMARY KEY (numerocertificado);


--
-- Name: lotes_pkey; Type: CONSTRAINT; Schema: public; Owner: root; Tablespace: 
--

ALTER TABLE ONLY lotes
    ADD CONSTRAINT lotes_pkey PRIMARY KEY (numerolote);


--
-- Name: orden_pkey; Type: CONSTRAINT; Schema: public; Owner: root; Tablespace: 
--

ALTER TABLE ONLY orden
    ADD CONSTRAINT orden_pkey PRIMARY KEY (numeroorden, numerocertificado);


--
-- Name: parametros_pkey; Type: CONSTRAINT; Schema: public; Owner: root; Tablespace: 
--

ALTER TABLE ONLY parametros
    ADD CONSTRAINT parametros_pkey PRIMARY KEY (idparametro);


--
-- Name: pedido_pkey; Type: CONSTRAINT; Schema: public; Owner: root; Tablespace: 
--

ALTER TABLE ONLY pedido
    ADD CONSTRAINT pedido_pkey PRIMARY KEY (numeroorden);


--
-- Name: proveedores_pkey; Type: CONSTRAINT; Schema: public; Owner: root; Tablespace: 
--

ALTER TABLE ONLY proveedores
    ADD CONSTRAINT proveedores_pkey PRIMARY KEY (claveproveedor);


--
-- Name: usuarios_correo_key; Type: CONSTRAINT; Schema: public; Owner: root; Tablespace: 
--

ALTER TABLE ONLY usuarios
    ADD CONSTRAINT usuarios_correo_key UNIQUE (correo);


--
-- Name: usuarios_pkey; Type: CONSTRAINT; Schema: public; Owner: root; Tablespace: 
--

ALTER TABLE ONLY usuarios
    ADD CONSTRAINT usuarios_pkey PRIMARY KEY (id);


--
-- Name: valoresdereferencia_pkey; Type: CONSTRAINT; Schema: public; Owner: root; Tablespace: 
--

ALTER TABLE ONLY valoresdereferencia
    ADD CONSTRAINT valoresdereferencia_pkey PRIMARY KEY (id_sap, idparametro);


--
-- Name: causadebaja_id_sap_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY causadebaja
    ADD CONSTRAINT causadebaja_id_sap_fkey FOREIGN KEY (id_sap) REFERENCES clientes(id_sap);


--
-- Name: certificado_numerolote_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY certificado
    ADD CONSTRAINT certificado_numerolote_fkey FOREIGN KEY (numerolote) REFERENCES lotes(numerolote);


--
-- Name: domicilio_id_sap_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY domicilio
    ADD CONSTRAINT domicilio_id_sap_fkey FOREIGN KEY (id_sap) REFERENCES clientes(id_sap);


--
-- Name: equipolaboratorio_claveproveedor_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY equipolaboratorio
    ADD CONSTRAINT equipolaboratorio_claveproveedor_fkey FOREIGN KEY (claveproveedor) REFERENCES proveedores(claveproveedor);


--
-- Name: fechadeanalisis_numerocertificado_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY fechadeanalisis
    ADD CONSTRAINT fechadeanalisis_numerocertificado_fkey FOREIGN KEY (numerocertificado) REFERENCES certificado(numerocertificado);


--
-- Name: orden_numerocertificado_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY orden
    ADD CONSTRAINT orden_numerocertificado_fkey FOREIGN KEY (numerocertificado) REFERENCES certificado(numerocertificado);


--
-- Name: orden_numeroorden_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY orden
    ADD CONSTRAINT orden_numeroorden_fkey FOREIGN KEY (numeroorden) REFERENCES pedido(numeroorden);


--
-- Name: pedido_iddomicilio_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY pedido
    ADD CONSTRAINT pedido_iddomicilio_fkey FOREIGN KEY (iddomicilio) REFERENCES domicilio(iddomicilio);


--
-- Name: resultados_claveequipo_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY resultados
    ADD CONSTRAINT resultados_claveequipo_fkey FOREIGN KEY (claveequipo) REFERENCES equipolaboratorio(claveequipo);


--
-- Name: resultados_idparametro_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY resultados
    ADD CONSTRAINT resultados_idparametro_fkey FOREIGN KEY (idparametro) REFERENCES parametros(idparametro);


--
-- Name: resultados_numerocertificado_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY resultados
    ADD CONSTRAINT resultados_numerocertificado_fkey FOREIGN KEY (numerocertificado) REFERENCES certificado(numerocertificado);


--
-- Name: valoresdereferencia_id_sap_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY valoresdereferencia
    ADD CONSTRAINT valoresdereferencia_id_sap_fkey FOREIGN KEY (id_sap) REFERENCES clientes(id_sap);


--
-- Name: valoresdereferencia_idparametro_fkey; Type: FK CONSTRAINT; Schema: public; Owner: root
--

ALTER TABLE ONLY valoresdereferencia
    ADD CONSTRAINT valoresdereferencia_idparametro_fkey FOREIGN KEY (idparametro) REFERENCES parametros(idparametro);


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

