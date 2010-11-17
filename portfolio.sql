drop sequence portfolio_ids;
drop table portfolio_portfolios;
drop table portfolio_users;
drop table portfolio_stocksDaily;
create sequence portfolio_ids start with 1;

create TABLE portfolio_users (
  email VARCHAR(32) NOT NULL primary key,
    constraint email_valid CHECK (REGEXP_LIKE (email, '[a-zA-Z0-9._%-]+@[a-zA-Z0-9._%-]+\.[a-zA-Z]{2,4}')),
  name VARCHAR(32) NOT NULL,
  password VARCHAR(64) NOT NULL,
      constraint long_pw CHECK (password LIKE '________%')
);

create TABLE portfolio_portfolios (
  id number primary key,
  name VARCHAR(32) NOT NULL,
  description VARCHAR(255),
  owner NOT NULL references portfolio_users(email),
  cash_balance number default '0' NOT NULL,
    constraint cash_balance_nonnegative CHECK( cash_balance >=0 )
);

create TABLE portfolio_stocksDaily (
  symbol varchar(16) NOT NULL,
  time number NOT NULL,
    constraint stock_time_unique UNIQUE(symbol, time),
  open number NOT NULL,
  close number NOT NULL,
  high number NOT NULL,
  low number NOT NULL,
  volume number NOT NULL
);

insert into portfolio_users (email, password, name) VALUES ('carylee@gmail.com', 'helloworld', 'Cary Lee');
insert into portfolio_users (email, password, name) VALUES ('tepeacock@gmail.com', 'peacockpreston', 'Todd Peacock-Preston');
insert into portfolio_users (email, password, name) VALUES ('admin@localhost.com', 'mypasswd', 'Some Administrator');
insert into portfolio_users (email, password, name) VALUES ('clarkefreak@gmail.com', 'istbnt2tb2nw', 'Nathan Ritter');
insert into portfolio_portfolios (id, name, description, owner) VALUES (portfolio_ids.nextval, 'Practice', 'description', 'carylee@gmail.com');

quit;