#!/bin/bash

# rsync diretorio de notas
rsync -avz -e 'ssh -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null' root@192.168.0.1:/var/www/nfe/producao /var/www/nfe
# permite acesso
chmod -R 777 /var/www/nfe
