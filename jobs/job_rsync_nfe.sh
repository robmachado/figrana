#!/bin/bash

# rsync diretorio de notas
rsync -avz -e 'ssh -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null' root@192.168.0.1:/var/www/nfe/producao /var/nfe
# permite acesso
chmod -R 777 /var/nfe/producao

# executa a importação dos faturamentos
#cd /var/www/figrana/jobs/
#php ./importaFaturamento.php
