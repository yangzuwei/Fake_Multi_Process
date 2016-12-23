create table(
id_num varchar(255) not null primary key,
std_name varchar(255) not null,
aux_num bigint(16) not null,
school varchar(72) not null,
class int(7) not null
)engine=innodb default charset=utf8;