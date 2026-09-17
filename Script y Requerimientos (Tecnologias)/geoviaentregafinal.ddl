-- Generado por Oracle SQL Developer Data Modeler 24.3.1.351.0831
--   en:        2026-09-16 19:47:58 CST
--   sitio:      Oracle Database 21c
--   tipo:      Oracle Database 21c



-- predefined type, no DDL - MDSYS.SDO_GEOMETRY

-- predefined type, no DDL - XMLTYPE

CREATE TABLE Cuadrilla 
    ( 
     Id_Cuadrilla INTEGER  NOT NULL , 
     Nombre       VARCHAR2 (100)  NOT NULL , 
     Responsable  VARCHAR2 (100)  NOT NULL , 
     Telefono     VARCHAR2 (20) 
    ) 
    LOGGING 
;

ALTER TABLE Cuadrilla 
    ADD CONSTRAINT Cuadrilla_PK PRIMARY KEY ( Id_Cuadrilla ) ;

CREATE TABLE Fotografia 
    ( 
     Id_Foto            INTEGER  NOT NULL , 
     Reporte_Id_Reporte INTEGER  NOT NULL , 
     Ruta_Foto          VARCHAR2 (255)  NOT NULL 
    ) 
    LOGGING 
;

ALTER TABLE Fotografia 
    ADD CONSTRAINT Fotografia_PK PRIMARY KEY ( Id_Foto ) ;

CREATE TABLE Inspeccion 
    ( 
     Id_Inspeccion      INTEGER  NOT NULL , 
     Reporte_Id_Reporte INTEGER  NOT NULL , 
     Usuario_Id_Usuario INTEGER  NOT NULL , 
     Fecha_Inspeccion   DATE DEFAULT SYSDATE  NOT NULL , 
     Resultado          VARCHAR2 (20)  NOT NULL , 
     Observaciones      VARCHAR2 (500) 
    ) 
    LOGGING 
;

ALTER TABLE Inspeccion 
    ADD CONSTRAINT Inspeccion_Resultado_CK 
    CHECK (Resultado IN ('APROBADO', 'RECHAZADO')) 
;

ALTER TABLE Inspeccion 
    ADD CONSTRAINT Inspeccion_PK PRIMARY KEY ( Id_Inspeccion ) ;

CREATE TABLE Orden_Trabajo 
    ( 
     Id_Orden               INTEGER  NOT NULL , 
     Reporte_Id_Reporte     INTEGER  NOT NULL , 
     Cuadrilla_Id_Cuadrilla INTEGER  NOT NULL , 
     Usuario_Id_Usuario     INTEGER  NOT NULL , 
     Fecha_Asignacion       DATE DEFAULT SYSDATE  NOT NULL , 
     Fecha_Finalizacion     DATE , 
     Estado                 VARCHAR2 (30) DEFAULT 'PENDIENTE'  NOT NULL , 
     Avance                 INTEGER DEFAULT 0  NOT NULL , 
     Observaciones          VARCHAR2 (500) 
    ) 
    LOGGING 
;

ALTER TABLE Orden_Trabajo 
    ADD CONSTRAINT Orden_Estado_CK 
    CHECK (Estado IN ('EN PROCESO', 'FINALIZADA', 'PENDIENTE')) 
;

ALTER TABLE Orden_Trabajo 
    ADD CONSTRAINT Orden_Avance_CK 
    CHECK (Avance BETWEEN 0 AND 100) 
;

ALTER TABLE Orden_Trabajo 
    ADD CONSTRAINT Orden_Estado_Fecha_CK 
    CHECK ((Estado = 'FINALIZADA' AND Fecha_Finalizacion IS NOT NULL) OR (Estado != 'FINALIZADA' AND Fecha_Finalizacion IS NULL))
;


ALTER TABLE Orden_Trabajo 
    ADD CONSTRAINT Orden_Fechas_CK 
    CHECK (Fecha_Finalizacion IS NULL OR Fecha_Finalizacion >= Fecha_Asignacion)
;
ALTER TABLE Orden_Trabajo 
    ADD CONSTRAINT Orden_Trabajo_PK PRIMARY KEY ( Id_Orden ) ;

CREATE TABLE Reporte 
    ( 
     Id_Reporte         INTEGER  NOT NULL , 
     Usuario_Id_Usuario INTEGER  NOT NULL , 
     Via_Id_Via         INTEGER  NOT NULL , 
     Descripcion        VARCHAR2 (500)  NOT NULL , 
     Tipo_Dano          VARCHAR2 (50)  NOT NULL , 
     Latitud            NUMBER (10,7)  NOT NULL , 
     Longitud           NUMBER (10,7)  NOT NULL , 
     Fecha_Reporte      DATE DEFAULT SYSDATE  NOT NULL , 
     Estado             VARCHAR2 (30) DEFAULT 'PENDIENTE'  NOT NULL , 
     Prioridad          INTEGER DEFAULT 0  NOT NULL 
    ) 
    LOGGING 
;

ALTER TABLE Reporte 
    ADD CONSTRAINT Reporte_Latitud_CK 
    CHECK (Latitud BETWEEN -90 AND 90) 
;

ALTER TABLE Reporte 
    ADD CONSTRAINT Reporte_Longitud_CK 
    CHECK (Longitud BETWEEN -180 AND 180) 
;

ALTER TABLE Reporte 
    ADD CONSTRAINT Reporte_Estado_CK 
    CHECK (Estado IN ('EN REPARACION', 'FINALIZADO', 'PENDIENTE', 'RECHAZADO', 'VALIDADO')) 
;

ALTER TABLE Reporte 
    ADD CONSTRAINT Reporte_Prioridad_CK 
    CHECK (Prioridad >= 0) 
;

ALTER TABLE Reporte 
    ADD CONSTRAINT Reporte_PK PRIMARY KEY ( Id_Reporte ) ;

CREATE TABLE Usuario 
    ( 
     Id_Usuario INTEGER  NOT NULL , 
     Nombre     VARCHAR2 (100)  NOT NULL , 
     Correo     VARCHAR2 (100)  NOT NULL , 
     Telefono   VARCHAR2 (20) , 
     Contrasena VARCHAR2 (255)  NOT NULL , 
     Rol        VARCHAR2 (20)  NOT NULL 
    ) 
    LOGGING 
;

ALTER TABLE Usuario 
    ADD CONSTRAINT Usuario_Rol_CK 
    CHECK (Rol IN ('AUTORIDAD', 'INSPECTOR', 'SUPERVISOR', 'VECINO')) 
;

ALTER TABLE Usuario 
    ADD CONSTRAINT Usuario_PK PRIMARY KEY ( Id_Usuario ) ;

ALTER TABLE Usuario 
    ADD CONSTRAINT Usuario_Correo_UQ UNIQUE ( Correo ) ;

CREATE TABLE Via 
    ( 
     Id_Via                     INTEGER  NOT NULL , 
     Nombre_Via                 VARCHAR2 (150)  NOT NULL , 
     Nivel_Trafico              VARCHAR2 (20)  NOT NULL , 
     Fecha_Ultimo_Mantenimiento DATE 
    ) 
    LOGGING 
;

ALTER TABLE Via 
    ADD CONSTRAINT Via_Trafico_CK 
    CHECK (Nivel_Trafico IN ('ALTO', 'BAJO', 'MEDIO')) 
;

ALTER TABLE Via 
    ADD CONSTRAINT Via_PK PRIMARY KEY ( Id_Via ) ;

ALTER TABLE Fotografia 
    ADD CONSTRAINT Fotografia_Reporte_FK FOREIGN KEY 
    ( 
     Reporte_Id_Reporte
    ) 
    REFERENCES Reporte 
    ( 
     Id_Reporte
    ) 
    ON DELETE CASCADE 
    NOT DEFERRABLE 
;

ALTER TABLE Inspeccion 
    ADD CONSTRAINT Inspeccion_Reporte_FK FOREIGN KEY 
    ( 
     Reporte_Id_Reporte
    ) 
    REFERENCES Reporte 
    ( 
     Id_Reporte
    ) 
    NOT DEFERRABLE 
;

ALTER TABLE Inspeccion 
    ADD CONSTRAINT Inspeccion_Usuario_FK FOREIGN KEY 
    ( 
     Usuario_Id_Usuario
    ) 
    REFERENCES Usuario 
    ( 
     Id_Usuario
    ) 
    NOT DEFERRABLE 
;

ALTER TABLE Orden_Trabajo 
    ADD CONSTRAINT Orden_Cuadrilla_FK FOREIGN KEY 
    ( 
     Cuadrilla_Id_Cuadrilla
    ) 
    REFERENCES Cuadrilla 
    ( 
     Id_Cuadrilla
    ) 
    NOT DEFERRABLE 
;

ALTER TABLE Orden_Trabajo 
    ADD CONSTRAINT Orden_Reporte_FK FOREIGN KEY 
    ( 
     Reporte_Id_Reporte
    ) 
    REFERENCES Reporte 
    ( 
     Id_Reporte
    ) 
    NOT DEFERRABLE 
;

ALTER TABLE Orden_Trabajo 
    ADD CONSTRAINT Orden_Supervisor_FK FOREIGN KEY 
    ( 
     Usuario_Id_Usuario
    ) 
    REFERENCES Usuario 
    ( 
     Id_Usuario
    ) 
    NOT DEFERRABLE 
;

ALTER TABLE Reporte 
    ADD CONSTRAINT Reporte_Usuario_FK FOREIGN KEY 
    ( 
     Usuario_Id_Usuario
    ) 
    REFERENCES Usuario 
    ( 
     Id_Usuario
    ) 
    NOT DEFERRABLE 
;

ALTER TABLE Reporte 
    ADD CONSTRAINT Reporte_Via_FK FOREIGN KEY 
    ( 
     Via_Id_Via
    ) 
    REFERENCES Via 
    ( 
     Id_Via
    ) 
    NOT DEFERRABLE 
;



-- Informe de Resumen de Oracle SQL Developer Data Modeler: 
-- 
-- CREATE TABLE                             7
-- CREATE INDEX                             0
-- ALTER TABLE                             27
-- CREATE VIEW                              0
-- ALTER VIEW                               0
-- CREATE PACKAGE                           0
-- CREATE PACKAGE BODY                      0
-- CREATE PROCEDURE                         0
-- CREATE FUNCTION                          0
-- CREATE TRIGGER                           0
-- ALTER TRIGGER                            0
-- CREATE COLLECTION TYPE                   0
-- CREATE STRUCTURED TYPE                   0
-- CREATE STRUCTURED TYPE BODY              0
-- CREATE CLUSTER                           0
-- CREATE CONTEXT                           0
-- CREATE DATABASE                          0
-- CREATE DIMENSION                         0
-- CREATE DIRECTORY                         0
-- CREATE DISK GROUP                        0
-- CREATE ROLE                              0
-- CREATE ROLLBACK SEGMENT                  0
-- CREATE SEQUENCE                          0
-- CREATE MATERIALIZED VIEW                 0
-- CREATE MATERIALIZED VIEW LOG             0
-- CREATE SYNONYM                           0
-- CREATE TABLESPACE                        0
-- CREATE USER                              0
-- 
-- DROP TABLESPACE                          0
-- DROP DATABASE                            0
-- 
-- REDACTION POLICY                         0
-- 
-- ORDS DROP SCHEMA                         0
-- ORDS ENABLE SCHEMA                       0
-- ORDS ENABLE OBJECT                       0
-- 
-- ERRORS                                   0
-- WARNINGS                                 0
