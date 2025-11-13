create database aula_injection;

use aula_injection;

create table usuarios(
id int primary key auto_increment, 
usuario varchar(120),
senha char(10)
);

insert into usuarios(usuario, senha) values("Teste", '123');
select * from usuarios;