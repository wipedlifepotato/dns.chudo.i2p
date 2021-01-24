# DNS.CHUDO.I2P
## What is?
- Is source code of http://dns.chudo.i2p so its an addressbook service for i2p network.
## Features
- Adding a domain from a file [like to 'hosts.txt', by html forms(request.php)]
- Jump service
- See online status
- Register subdomains for real domain, though some "file confirmation". 
## How to install
```
 git clone https://github.com/wipedlifepotato/dns.chudo.i2p.git
cd dns.chudo.i2p
mysql -u root -p... < contrib/sql.sql 
```
then install apache2/nginx etc... with php (TODO: depencies list)
and put the files in virtual directory of webserver
